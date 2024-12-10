<?php
namespace Customer\Sell\Observer;

use \Customer\Sell\Helper\Email;
use \Customer\Sell\Helper\Whatsapp;

class Sendmail implements \Magento\Framework\Event\ObserverInterface
{

    protected $whatsappHelper;

    public function __construct(
        Email $helper,
        Whatsapp $whatsappHelper
    ) {
        $this->helper = $helper;
        $this->whatsappHelper = $whatsappHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observer_data = $observer->getData('sell');
        $this->helper->sendMail($observer_data);
        $this->whatsappHelper->sendMessage($observer_data);
        return $this;
    }
}
