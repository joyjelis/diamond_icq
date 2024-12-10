<?php

namespace Gemfind\Ringbuilder\Controller\Diamond;

use Magento\Framework\App\Action\Context;

class Printdiamond extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * Printdiamond Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {   $id = $this->getRequest()->getParam('id');
        if (!$id) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            $this->messageManager->addError(__('Invalid Product'));
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
 
}
