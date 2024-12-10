<?php

namespace Customer\Sell\Controller\Quote;

use Magento\Framework\View\Result\PageFactory;

class Complete extends \Magento\Framework\App\Action\Action
{

 
    protected $_resultPageFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('sell-your-jewellery_quote_complete');
        return $resultPage;
    }
}
