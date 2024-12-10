<?php

namespace Customer\Sell\Block\Index;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template\Context;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $session;

    protected $jsconfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Customer\Model\SessionFactory            $session
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionFactory $sessionFactory,
        \Customer\Sell\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->session = $sessionFactory;
        parent::__construct($context, $data);
    }

    public function getEmail()
    {
        return $this->session->create()->getCustomer()->getEmail();
    }

    public function getCustomerName()
    {
        return $this->session->create()->getCustomer()->getName();
    }

    public function getFirstname()
    {
        return $this->session->create()->getCustomer()->getFirstname();
    }

    public function getLastname()
    {
        return $this->session->create()->getCustomer()->getLastname();
    }

    /**
     * Get Login Customer ID
     * @return integer
     */
    public function getCustomer()
    {
        return $this->session->create()->getCustomer()->getId();
    }

    public function getCustomerConfig()
    {
        $this->jsconfig['customer']['id'] = $this->getCustomer();
        $this->jsconfig['customer']['fname'] = $this->getFirstname();
        $this->jsconfig['customer']['lname'] = $this->getLastname();
        $this->jsconfig['customer']['email'] = $this->getEmail();
    }

    public function getJsConfig()
    {
        $this->jsconfig['koalendar']['private_url'] = $this->helper->getConfig('koalendar/general/customer_login_book_an_appointment');
        $this->jsconfig['koalendar']['quote_id'] = $this->helper->getConfig('koalendar/general/customer_login_quote_id_field');
        $this->jsconfig['koalendar']['public_url'] = $this->helper->getConfig('koalendar/general/public_book_an_appointment');
        $this->getCustomerConfig();
        return json_encode($this->jsconfig);
    }
}
