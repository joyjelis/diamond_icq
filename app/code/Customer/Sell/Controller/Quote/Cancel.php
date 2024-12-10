<?php

namespace Customer\Sell\Controller\Quote;

use Customer\Sell\Model\Sell;
use Magento\Framework\App\RequestInterface;

class Cancel extends \Magento\Framework\App\Action\Action
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
        \Magento\Customer\Model\Session $customerSession,
        \Customer\Sell\Model\SellFactory $sellFactory,
        \Customer\Sell\Model\HistoryFactory $historyFactory,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerUrl = $customerUrl;
        $this->sellFactory = $sellFactory;
        $this->historyFactory = $historyFactory;
        parent::__construct($context);
    }

    /**
     * Dispatch function
     *
     * @param RequestInterface $request
     * @return void
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_customerUrl->getLoginUrl();
        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (array) $this->getRequest()->getParam('id');
        if ($id) {
            $sell = $this->sellFactory->create()->load($id);
            $sell->setStatus(Sell::CUSTOMER_CANCELLED_APPOINTMENT_STATUS);
            $bookingID = $sell->getbooking_appintment_id();
            $sell->setGenerateQuoteId(0);
            $sell->setQuoteNoUpdate(1);
            if ($sell->save()) {
                $sell = $this->sellFactory->create()->load($sell->getId());

                $history = $this->historyFactory->create();
                $history->setSellId($sell->getId());
                $history->setStatus($sell->getStatus());
                $history->save();

                $this->_eventManager->dispatch(
                    'sell_diamond_email',
                    ['sell' => $sell]
                );
            }

        }

        $url = 'https://koalendar.com/e/' . $sell->getbooking_appintment_url_slug() . '/cancel/' . $bookingID;

        return $this->resultRedirectFactory->create()->setUrl($url);
    }
}
