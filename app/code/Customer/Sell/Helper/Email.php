<?php

namespace Customer\Sell\Helper;

use Customer\Sell\Model\Sell;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Email extends AbstractHelper
{

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Customer\Sell\Model\SellFactory $sellFactory,
        \Customer\Sell\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepo,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->sellFactory = $sellFactory;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->_messageManager = $messageManager;
        $this->_localeDate = $localeDate;
        $this->_addressRepo = $addressRepo;
        $this->helper = $helper;
        $this->_addressConfig = $addressConfig;
        $this->addressMapper = $addressMapper;
        parent::__construct($context);
    }

    /**
     * Retrieve formatting date
     *
     * @param null|string|\DateTimeInterface $date
     * @param int $format
     * @param bool $showTime
     * @param null|string $timezone
     * @return string
     */
    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date ?? 'now');
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }

    /**
     * Retrieve formatting time
     *
     * @param   \DateTime|string|null $time
     * @param   int $format
     * @param   bool $showDate
     * @return  string
     */
    public function formatTime(
        $time = null,
        $format = \IntlDateFormatter::SHORT,
        $showDate = false
    ) {
        $time = $time instanceof \DateTimeInterface ? $time : new \DateTime($time ?? 'now');
        return $this->_localeDate->formatDateTime(
            $time,
            $showDate ? $format : \IntlDateFormatter::NONE,
            $format
        );
    }

    public function getAddress($addressId)
    {
        try {

            $msg = __('Customer have not setup address.');

            if (!empty($addressId)) {
                $address = $this->_addressRepo->getById($addressId);
            }

        } catch (\Exception $e) {
            $this->setAddressId("");
            return $msg;
        }

        if ($address) {
            return $this->_getAddressHtml($address);
        } else {
            return $msg;
        }
    }

    protected function _getAddressHtml($address)
    {
        $renderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
        return $renderer->renderArray($this->addressMapper->toFlatArray($address));
    }

    public function getEmailTemplate($template)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->scopeConfig->getValue('sell_item_email/general/' . $template, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function sendMail(Sell $sell)
    {
        $sell = $this->Transformdata($sell);
        if (!empty($sell->getEmail())) {
            $this->sendCustomerMail($sell);
            $this->sendAdminMail($sell);
        }
        return true;
    }

    public function sendCustomerMail(Sell $sell)
    {
        switch ($sell->getStatus()) {
            case "New Trade Request":
                $email_template = $this->getEmailTemplate('sell_item_email');
                $this->SendEMail($email_template, $sell);
                break;

            case Sell::QUALIFY_LABEL:
                $email_template = $this->getEmailTemplate('sell_item_qualify');
                $this->SendEMail($email_template, $sell);

                break;

            case Sell::NOT_QUALIFY_LABEL:
                $email_template = $this->getEmailTemplate('sell_item_not_qualify');
                $this->SendEMail($email_template, $sell);

                break;

            case Sell::NEW_OFFER_LABEL:
                $email_template = $this->getEmailTemplate('sell_item_qualify_secound_valuation');
                $this->SendEMail($email_template, $sell);

                break;

            case Sell::ADMIN_OFFER_CONSIGNMENT_LABEL:
                $email_template = $this->getEmailTemplate('sell_item_consignment');
                $this->SendEMail($email_template, $sell);

                break;

            case Sell::INSPECTION_NOT_PASSED_LABEL:
                $email_template = $this->getEmailTemplate('sell_item_not_passed_inspection');
                $this->SendEMail($email_template, $sell);

                break;

            case Sell::CUSTOMER_BOOKED_AN_APPOINTMENT_STATUS_LABEL:
                $email_template = $this->getEmailTemplate('sell_item_email_appoinement_accept');
                $this->SendEMail($email_template, $sell);

                break;

            case Sell::CUSTOMER_RESCHEDULED_APPOINTMENT_LABEL:
                $email_template = $this->getEmailTemplate('sell_item_email_appoinement_accept');
                $this->SendEMail($email_template, $sell);

                break;

            case Sell::CUSTOMER_REQUESTED_PICK_UP_LABEL:
                $email_template = $this->getEmailTemplate('pick_request_init');
                $this->SendEMail($email_template, $sell);

                break;

            case Sell::CUSTOMER_PICK_UP_GENERATED_LABEL:
                $email_template = $this->getEmailTemplate('pick_request_generated');
                $this->SendEMail($email_template, $sell);

                break;

            case Sell::PAID_LABEL:
                $email_template = $this->getEmailTemplate('sell_item_paid_to_customer');
                $this->SendEMail($email_template, $sell);

                break;
        }
    }

    public function sendAdminMail(Sell $sell)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        switch ($sell->getStatus()) {
            case Sell::CUSTOMER_ASKED_FOR_RETURN_SHIPPING_PAYMENT_LINK_LABEL:
                $email_template = $this->getEmailTemplate('sell_item_email_admin');
                $this->SendEMail($email_template, $sell, 1, "shipping@diamondicq.com");
                break;

            default:
                $email_template = $this->getEmailTemplate('sell_item_email_admin');
                $this->SendEMail($email_template, $sell, 1);
                break;
        }
    }

    public function SendEMail($template, $data, $IsAdmin = 0, $cc = null, $adminemail = null)
    {
        try {
            $from_address = $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE);

            if ($IsAdmin == 1) {
                if (!$adminemail) {
                    $adminemail = $this->scopeConfig->getValue('sell_item_email/general/admin_email_address', ScopeInterface::SCOPE_STORE);
                }

                if ($adminemail) {
                    $email = $adminemail;
                } else {
                    $email = $from_address;
                }
                $name = "Admin";
            } else {
                $email = $data->getEmail();
                $name = $data->getName();
            }

            $storeId = $this->_storeManager->getStore()->getId();
            $this->_inlineTranslation->suspend();

            $sender = ['email' => $from_address, 'name' => 'Diamond ICQ'];

            $this->_transportBuilder->setTemplateIdentifier(
                $template
            )->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId,
                ]
            )->setTemplateVars(
                ['data' => $data]
            )->setFrom(
                $sender
            )->addTo(
                $email,
                $name
            );

            // $bccMail = ‘[email address]’;
            // $this->_transportBuilder->addBcc($bccMail);

            //create the transport
            if (!isset($transport)) {
                $transport = $this->_transportBuilder->getTransport();
            }

            $transport->sendMessage();
            $this->_inlineTranslation->resume();

            return true;

        } catch (Exception $e) {
            $this->_messageManager->addExceptionMessage($e, __($e->getMessage()));
        }
    }

    public function Transformdata(Sell $data)
    {
        $statuslabels = $this->sellFactory->create()->getStatusOptions();
        if (!empty($data->getStatus()) && isset($statuslabels[$data->getStatus()])) {
            $status = $data->getStatus();
            $data->setStatus($statuslabels[$data->getStatus()]);
            if ($status == 1) {
                $data->setStatus("New Trade Request");
            }
        }

        if (!empty($data->getPrice())) {
            $data->setPrice($this->helper->getCurrencyWithFormat($data->getPrice()));
        }

        if (!empty($data->getOfferPrice())) {
            $data->setOfferPrice($this->helper->getCurrencyWithFormat($data->getOfferPrice()));
        }

        if (!empty($data->getCreatedAt())) {
            $dateModel = $this->dateTimeFactory->create();
            $submmited_at = $dateModel->gmtDate("j-F-Y", $data->getCreatedAt());
            $data->setCreatedAt($submmited_at);
        }

        if (!$data->getCurrentDate()) {
            $submmited_at = $dateModel->gmtDate("j-F-Y");
            $data->setCurrentDate($submmited_at);
        }

        if ($data->getStreet() && $data->getLandmark() && $data->getCity() && $data->getPickupCountry() && $data->getPostcode()) {
            $data->setAddress($this->helper->getAddress($data->getId()));
        }

        if ($data->getPickDate()) {
            $date = $data->getPickDate();
            $data->setPickDate($this->formatDate($date, \IntlDateFormatter::MEDIUM));
            $data->setPickTime($this->formatTime($date, \IntlDateFormatter::MEDIUM));
        }

        if ($data->getScheduleDate()) {
            $ScheduleDate = $data->getScheduleDate();
            $data->setAppointmentScheduleDate($this->formatDate($ScheduleDate, \IntlDateFormatter::MEDIUM));
            $data->setAppointmentScheduleTime($this->formatTime($ScheduleDate, \IntlDateFormatter::MEDIUM));
        }

        return $data;
    }
}
