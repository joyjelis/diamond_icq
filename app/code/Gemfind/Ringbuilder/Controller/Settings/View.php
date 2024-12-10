<?php

namespace Gemfind\Ringbuilder\Controller\Settings;

use Magento\Framework\App\Action\Action;

class View extends Action
{

    /**
     * @var \Gemfind\Ringbuilder\Helper\Data
     */
    protected $helper;
	
	/**
     * @var product data
     */
    protected $product;

    /**
     * View constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Gemfind\Ringbuilder\Helper\Data $helper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Gemfind\Ringbuilder\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {   
		
        if (!$this->_helper->isGemfindEnabled()) {
            $this->messageManager->addError(__("Please enable this Extension, go to configuration."));
            $this->_redirect('/');
        }
		

        if (!$this->_helper->getUsername()) {
            $this->messageManager->addError(__("Please add Gemfind UserID in Extension Configuration"));
            $this->_redirect('/');
        }
		
		$setting = $this->getProduct();
		if(!sizeof($setting['ringData']) > 0) {
			$this->messageManager->addError(__('The ring that was sent to you is unfortunately no longer available.'));
            $resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setPath('ringbuilder/settings');
			return $resultRedirect;
		}
        
		
        $id = $this->getRequest()->getParam('path');
        if (!$id) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            $this->messageManager->addError(__('Invalid Product'));
            return $resultRedirect;
        }
		
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
	
	/**

     * @return array|product

     */

    public function getProduct() {
        $id = $this->excludeid();
        if (!$this->product) {
                $this->product = (array)$this->_helper->getRingById($id);      
                /*$this->triggerClick($this->product);*/
        }
        return $this->product;

    }
	
	/**
     * @return string
     */
    public function excludeid(){
        $urlstring = $this->getRequest()->getParam('path');     
        $urlarray = explode('-sku-', $urlstring);
        return $urlarray[1];
    }
}
