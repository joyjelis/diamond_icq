<?php

namespace Gemfind\Ringbuilder\Controller\Diamond;

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
        
        $id = $this->getRequest()->getParam('path');
        if (!$id) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            $this->messageManager->addError(__('Invalid Product'));
            return $resultRedirect;
        }
        $diamond = $this->getProduct();
        if(!sizeof($diamond['diamondData']) > 0) {
            $this->messageManager->addError(__('The diamond that was sent to you is unfortunately no longer available.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('ringbuilder/diamond');
            return $resultRedirect;
        }
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }


    public function excludeid(){
        $urlstring = $this->getRequest()->getParam('path');     
        $urlarray = explode('-sku-', $urlstring);
        return $urlarray[1];

    }
    
    /**
     * @return array|product
     */
    public function getProduct()
    {   
        $id = $this->excludeid();
        $type = $this->getRequest()->getParam('type');

        if (!$this->product) {
            if($type == 'labcreated'){
                $this->product = (array)$this->_helper->getDiamondByIdtype($id,$type);
                $this->product['diamondData']['type'] = 'labcreated';
            } else if($type == 'fancy') {
                $this->product = (array)$this->_helper->getDiamondByIdtype($id,$type);
                $this->product['diamondData']['type'] = 'fancy';
            } else {
                $this->product = (array)$this->_helper->getDiamondById($id);    
            }
            
        }
        return $this->product;
    }
}
