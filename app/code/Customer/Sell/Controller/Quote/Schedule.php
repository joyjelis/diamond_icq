<?php

namespace Customer\Sell\Controller\Quote;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Customer\Sell\Model\Sell;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\ResultInterface;

class Schedule extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
{

    protected $_customerSession;

    protected $_customerUrl;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Customer\Model\Session $customerSession,
        \Customer\Sell\Model\SellFactory $sellFactory,
        \Customer\Sell\Model\HistoryFactory $historyFactory,
        \Customer\Sell\Helper\Data $helper,
        PsrLoggerInterface $logger,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->_customerSession = $customerSession;
        $this->_customerUrl = $customerUrl;
        $this->jsonFactory = $jsonFactory;
        $this->sellFactory = $sellFactory;
        $this->historyFactory = $historyFactory;
        $this->_timezoneInterface = $timezoneInterface;
        parent::__construct($context);
    }

    /**
     * converToTz convert Datetime from one zone to another
     * @param string $dateTime which we want to convert
     * @param string $toTz timezone in which we want to convert
     * @param string $fromTz timezone from which we want to convert
     */
    protected function converToTz($dateTime)
    {
        // timezone by php friendly values
        $toTz = $this->_timezoneInterface->getDefaultTimezone();
        $fromTz = $this->_timezoneInterface->getConfigTimezone();
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('m/d/Y H:i:s');
        return $dateTime;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $json = file_get_contents('php://input');
            $this->logger->error(print_r($json, true));
            $resultJson = $this->jsonFactory->create();
            $data = json_decode($json);
            if (isset($data->invitee->fields)) {
                $fields = $data->invitee->fields;
                foreach ($fields as $key => $value) {
                    if (isset($value->q) && $value->q == "Quote ID") {
                        $quote_id = $value->a;
                    }

                    $quote_id_field = $this->helper->getConfig('koalendar/general/customer_login_quote_id_field');

                    if ($key == $quote_id_field) {
                        $quote_id = $value;
                    }
                }
                
                $temp = $this->IsTemparoryQuote($quote_id);

                if (!empty($quote_id)) {
                    $sell = $this->sellFactory->create()->load($quote_id, 'quote');
                    $sell->setGenerateQuoteId(0);
                    $sell->setQuoteNoUpdate(1);

                    if ($sell->getQuote() == $quote_id) {
                        if (!$temp) {
                            $sell->setStatus(Sell::CUSTOMER_BOOKED_AN_APPOINTMENT_STATUS);
                            if ($sell->getScheduleDate()) {
                                $sell->setStatus(Sell::CUSTOMER_RESCHEDULED_APPOINTMENT_STATUS);
                            }
                        }

                        $sell->setbooking_appintment_id($data->id);
                        $sell->setbooking_appintment_url_slug($data->link->slug);
                        $sell->setScheduleDate($this->converToTz($data->start_at));
                        if ($sell->save()) {
                            $sell = $this->sellFactory->create()->load($sell->getId());
                        }

                        if (!$temp) {
                            // Process History
                            $history = $this->historyFactory->create();
                            $history->setSellId($sell->getId());
                            $history->setStatus($sell->getStatus());
                            $history->save();

                            $this->_eventManager->dispatch(
                                'sell_diamond_email',
                                ['sell' => $sell]
                            );
                        }

                        return $resultJson->setData([
                            'success' => true
                        ]);
                    }
                } else {
                    return $resultJson->setData([
                        'success' => false
                    ]);
                }
            }

            return $resultJson->setData([
                'success' => false
            ]);
        } catch (\Exception $e) {
            return $resultJson->setData([
                'success' => false
            ]);
        }
    }

    public function IsTemparoryQuote($quote)
    {
        if (strpos($quote, 'TP#') !== false) {
            return true;
        }

        return false;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
