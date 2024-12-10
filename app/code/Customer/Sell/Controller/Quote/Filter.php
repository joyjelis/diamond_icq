<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_Quote
 */

namespace Customer\Sell\Controller\Quote;

class Filter extends \Magento\Framework\App\Action\Action
{

    protected $resultJsonFactory;

    protected $regionColFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Customer\Sell\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
        $post = $this->getRequest()->getPost('data');
        $html = $this->helper->getItemsHtml();
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => true, 'html' => $html]);
    }
}
