<?php

namespace Gemfind\Ringbuilder\Controller\Diamond;


use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Framework\Controller\Result\JsonFactory;

class Diamondsearch extends \Magento\Framework\App\Action\Action
{
 /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Gemfind\Ringbuilder\Helper\Data
     */
    protected $helper;

    /**
     * @var Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Ringbuilder constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Gemfind\Ringbuilder\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Helper $helper,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory; 
        $this->helper = $helper;
        parent::__construct($context);
    }

     /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->helper->isGemfindEnabled()) {
            $this->messageManager->addError(__("Please enable this Extension, go to configuration."));
            $this->_redirect('/');
        }

        if (!$this->helper->getUsername()) {
            $this->messageManager->addError(__("Please add Gemfind UserID in Extension Configuration"));
            $this->_redirect('/');
        }
        $data = $this->getRequest()->getPostValue();
        if($data){
        if ($data['submitby'] != 'ajax') {
            $this->_redirect('ringbuilder/diamond');
        }
        $data = $this->getRequest()->getPostValue();

        $result = $this->resultJsonFactory->create();
        $resultPage = $this->resultPageFactory->create();
        $block = $resultPage->getLayout()
                ->createBlock('Gemfind\Ringbuilder\Block\Diamond\Search\Result')
                ->setTemplate('Gemfind_Ringbuilder::diamond/result.phtml')
                ->setData('data',$data)
                ->toHtml();
        $result->setData(['output' => $block]);
        return $result;
    }
    $this->_redirect('ringbuilder/diamond');
    }

}
