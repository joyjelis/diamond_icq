<?php

namespace Gemfind\Ringbuilder\Controller\Diamond;


use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Framework\Controller\Result\JsonFactory;

class Loadshape extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Gemfind\Ringbuilder\Helper\Data
     */
    protected $helper;


    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Loadshape constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Gemfind\Ringbuilder\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Helper $helper,
        JsonFactory $resultJsonFactory
    ) {
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
        $fancycolorcontent = array();
        $fancycolorcontentContent = '';
        if (array_key_exists('diamond_fancycolor', $data)) {
            foreach ($data["diamond_fancycolor"] as $value) {
                $fancycolorcontent[] = strtolower($value);
            }
            $fancycolorcontentContent = implode(',', $fancycolorcontent);
        } 
        if($fancycolorcontentContent == ""){
            $fancycolorcontentContent = 'Blue,Pink,Yellow,Brown,Green,Gray,Black,Red,Purple,Chameleon,Violet';   
        }
        $color = $fancycolorcontentContent;
        $shapedata = $this->helper->getShapeByColor($color);   
        $result = $this->resultJsonFactory->create();
        $result->setData(['output' => $shapedata]);
       return $result;
    }
    
}
