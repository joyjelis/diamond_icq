<?php

/**
 * @copyright Copyright (c) 2018 www.Gemfind.com
 */

namespace Gemfind\Ringbuilder\Controller\Diamond;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class ResultReqInfo extends Action {


   /**
     * @var \Gemfind\Ringbuilder\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * ResultReqInfo constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Gemfind\Ringbuilder\Helper\Data $helper
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */

    public function __construct(
    Context $context, \Gemfind\Ringbuilder\Helper\Data $helper, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Store\Model\StoreManagerInterface $storeManager, JsonFactory $resultJsonFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());
                //$diamondData = $this->_helper->getDiamondById($data['diamondid']);
                if(isset($data['type'])){
                    $diamondData = $this->_helper->getDiamondByIdtype($data['diamondid'],$data['type']);
                } else {
                    $diamondData = $this->_helper->getDiamondById($data['diamondid']);
                }
                $retaileremail = $diamondData['diamondData']['retailerInfo']->retailerEmail;

                if($this->_helper->getAdminEmail()){
                    $to = $this->_helper->getAdminEmail();
                } else {
                    $retaileremail = str_replace(',', ';', $retaileremail);
                    $to = $retaileremail;
                } 
					$lab = 'Not Available';
					if(isset($diamondData['diamondData']['certificateNo'])) { 
						$lab = $diamondData['diamondData']['certificateNo'] .' '. $diamondData['diamondData']['certificate'] .' <a href="'.$diamondData['diamondData']['certificateUrl'].'">Certificate</a>'; 
					}
				
                $templateVars = array(
                    'name' => (isset($data['name'])) ? $data['name'] : '',
                    'email' => (isset($data['email'])) ? $data['email'] : '',
                    'phone' => (isset($data['phone'])) ? $data['phone'] : '',                   
                    'hint_message' => (isset($data['hint_message'])) ? $data['hint_message'] : '',
                    'contact_pref' => (isset($data['contact_pref'])) ? $data['contact_pref'] : '',
                    'diamond_url' => (isset($data['diamondurl'])) ? $data['diamondurl'] : '',
                    'diamond_id' => (isset($diamondData['diamondData']['diamondId'])) ? $diamondData['diamondData']['diamondId']: '',
                    'size' => (isset($diamondData['diamondData']['caratWeight'])) ? $diamondData['diamondData']['caratWeight'] : '',
                    'cut' => (isset($diamondData['diamondData']['cut'])) ? $diamondData['diamondData']['cut'] : '',
                    'color' => (isset($diamondData['diamondData']['color'])) ? $diamondData['diamondData']['color'] : '',
                    'clarity' => (isset($diamondData['diamondData']['clarity'])) ? $diamondData['diamondData']['clarity'] : '',
                    'depth' => (isset($diamondData['diamondData']['depth'])) ? $diamondData['diamondData']['depth'] : '',
                    'table' => (isset($diamondData['diamondData']['table'])) ? $diamondData['diamondData']['table'] : '',
                    'measurment' => (isset($diamondData['diamondData']['measurement'])) ? $diamondData['diamondData']['measurement'] : '',
                    'certificate' => (isset($diamondData['diamondData']['certificate'])) ? $diamondData['diamondData']['certificate'] : '',
                    'price' => (isset($diamondData['diamondData']['fltPrice'])) ? $diamondData['diamondData']['fltPrice'] : '',
                    'vendorID' => (isset($diamondData['diamondData']['vendorID'])) ? $diamondData['diamondData']['vendorID'] : '',
                    'vendorName' => (isset($diamondData['diamondData']['vendorName'])) ? $diamondData['diamondData']['vendorName'] : '',
                    'vendorEmail' => (isset($diamondData['diamondData']['vendorEmail'])) ? $diamondData['diamondData']['vendorEmail'] : '',
                    'vendorContactNo' => (isset($diamondData['diamondData']['vendorContactNo'])) ? $diamondData['diamondData']['vendorContactNo'] : '',
                    'vendorStockNo' => (isset($diamondData['diamondData']['vendorStockNo'])) ? $diamondData['diamondData']['vendorStockNo'] : '',
                    'vendorFax' => (isset($diamondData['diamondData']['vendorFax'])) ? $diamondData['diamondData']['vendorFax'] : '',
                    'vendorAddress' => (isset($diamondData['diamondData']['vendorAddress'])) ? $diamondData['diamondData']['vendorAddress'] : '',
                    'wholeSalePrice' => (isset($diamondData['diamondData']['wholeSalePrice'])) ? $diamondData['diamondData']['wholeSalePrice'] : '',
                    'vendorAddress' => (isset($diamondData['diamondData']['vendorAddress'])) ? $diamondData['diamondData']['vendorAddress'] : '',
                    'retailerName' => (isset($diamondData['diamondData']['retailerInfo']->retailerName)) ? $diamondData['diamondData']['retailerInfo']->retailerName : '',
                    'retailerID' => (isset($diamondData['diamondData']['retailerInfo']->retailerID)) ? $diamondData['diamondData']['retailerInfo']->retailerID : '',
                    'retailerEmail' => (isset($diamondData['diamondData']['retailerInfo']->retailerEmail)) ? $diamondData['diamondData']['retailerInfo']->retailerEmail : '',
                    'retailerContactNo' => (isset($diamondData['diamondData']['retailerInfo']->retailerContactNo)) ? $diamondData['diamondData']['retailerInfo']->retailerContactNo : '',
                    'retailerFax' => (isset($diamondData['diamondData']['retailerInfo']->retailerFax)) ? $diamondData['diamondData']['retailerInfo']->retailerFax : '',
                    'retailerAddress' => (isset($diamondData['diamondData']['retailerInfo']->retailerAddress)) ? $diamondData['diamondData']['retailerInfo']->retailerAddress : '',
					'lab' => $lab,
                );
                $this->inlineTranslation->suspend();
                $transport_admin_template = $this->_helper->getEmailTemplate('gemfinddiamondsearch/email/request_info_email_template_admin');
                if($transport_admin_template=='')
                    $transport_admin_template = 'gemfinddiamondsearch_email_request_info_email_template_admin';
                
                $transport_admin = $this->_transportBuilder->setTemplateIdentifier($transport_admin_template)
                        ->setTemplateOptions($templateOptions)
                        ->setTemplateVars($templateVars)
                        ->setFrom($this->_helper->getEmailSender())
                        ->addTo($to)
                        ->getTransport();
                $transport_admin->sendMessage();
                $transport_sender_template = $this->_helper->getEmailTemplate('gemfinddiamondsearch/email/request_info_email_template_sender');
                if($transport_sender_template=='')
                    $transport_sender_template = 'gemfinddiamondsearch_email_request_info_email_template_sender';
                
                $transport_sender = $this->_transportBuilder->setTemplateIdentifier($transport_sender_template)
                        ->setTemplateOptions($templateOptions)
                        ->setTemplateVars($templateVars)
                        ->setFrom($this->_helper->getEmailSender())
                        ->addTo($data['email'])
                        ->getTransport();
                $transport_sender->sendMessage();
                $this->inlineTranslation->resume();
                $message = __('Thanks for your submission.');
                $data = array('status' => 1, 'msg' => $message );
                $result->setData(['output' => $data]);
                return $result;                    
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $message = $e->getMessage();
            } catch (\RuntimeException $e) {
                $message = $e->getMessage();
            } catch (\Exception $e) {
                $message = __('Something went wrong. Please try again later!');
        }
        $data = array('status' => 0, 'msg' => $message );
        $result->setData(['output' => $data]);
        return $result;
    }
        $message = __('Not found all the required fields');
        $data = array('status' => 0, 'msg' => $message );
        $result->setData(['output' => $data]);
        return $result;
    }

}
