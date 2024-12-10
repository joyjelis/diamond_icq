<?php

namespace Gemfind\Ringbuilder\Controller\Settings;

use Magento\Framework\App\Action\Action;

class Ringurl extends Action
{

    /**
     * @var \Gemfind\Ringbuilder\Helper\Data
     */
    protected $helper;

    protected $urlBuilder;

    /**
     * View constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Gemfind\Ringbuilder\Helper\Data $helper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Gemfind\Ringbuilder\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {   
        $id = $this->getRequest()->getParam('id');
        if($id){
        $settings = $this->_helper->getRingById($id);
        $collectionContent = $id = $this->getRequest()->getParam('requesteddata');
        if(sizeof($settings['ringData']) > 0) {
            if(sizeof($settings['ringData']['configurableProduct']) > 0){
                foreach ($settings['ringData']['configurableProduct'] as $value) {
                    $value = (array) $value;
                    $metalarray[strtolower(str_replace(' ', '', $value['metalType']))] = $value['gfInventoryId'];
                }
            }
       
        if($collectionContent != ''){
            $metaltype = strtolower(str_replace(' ', '-', $collectionContent)).'-metaltype-';
            $name = strtolower(str_replace(' ', '-', $settings['ringData']['settingName']));
            $sku = '-sku-'.str_replace(' ', '-', $metalarray[strtolower(str_replace(' ', '', $collectionContent))]);
            if($metalarray[strtolower(str_replace(' ', '', $collectionContent))]){
                $url = $this->urlBuilder->getUrl('ringbuilder/settings/view', ['path' => $metaltype.$name.$sku, '_secure' => true]);
            } else {
                $url = $this->urlBuilder->getUrl('ringbuilder/settings/view', ['path' => $name.$sku, '_secure' => true]);
            }
        } else {
            $metaltype = '14k-white-gold-metaltype-';
            $name = strtolower(str_replace(' ', '-', $settings['ringData']['settingName']));
            $sku = '-sku-'.str_replace(' ', '-', $metalarray['14kwhitegold']);            
            if(isset($metalarray['14kwhitegold'])){
                $url = $this->urlBuilder->getUrl('ringbuilder/settings/view', ['path' => $metaltype.$name.$sku, '_secure' => true]);
            } else {
                $url = $this->urlBuilder->getUrl('ringbuilder/settings/view', ['path' => $name.$sku, '_secure' => true]);
            }
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['diamondviewurl' => $url]);
        }  else {
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['diamondviewurl' => '']);
        } } else {
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['diamondviewurl' => $this->urlBuilder->getUrl('ringbuilder/settings', ['_secure' => true])]);
        }
    }
}
