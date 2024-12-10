<?php



/**

 * @copyright Copyright (c) 2018 www.Gemfind.com

 */



namespace Gemfind\Ringbuilder\Controller\Settings;



use Magento\Framework\App\Action\Action;

use Magento\Framework\App\Action\Context;

use Magento\Framework\Controller\Result\JsonFactory;



class ResultScheView extends Action {





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

     * ResultScheView constructor.

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

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {

            try {
				
				$this->sendCustomerMail($data);

                $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());

                $ringData = $this->_helper->getRingById($data['settingid']);

                $retaileremail = $ringData['ringData']['retailerInfo']->retailerEmail;

                    $to = $this->_helper->getAdminEmail();
                
				$lab = 'Not Available';
					if(isset($ringData['ringData']['certificateNo'])) { 
						$lab = $ringData['ringData']['certificateNo'] .' '. $ringData['ringData']['certificate'] .' <a href="'.$ringData['ringData']['certificateUrl'].'">Certificate</a>'; 
					}
                $templateVars = array(

                    'name' => $data['name'],

                    'email' => $data['email'],

                    'phone' => $data['phone'],                   

                    'hint_message' => $data['hint_message'],

                    'location' => $data['location'],

                    'avail_date' => $data['avail_date'],

                    'appnt_time' => $data['appnt_time'],

                    'ring_url' => (isset($data['ringurl'])) ? $data['ringurl'] : '',

                    'setting_id' => (isset($ringData['ringData']['settingId'])) ? $ringData['ringData']['settingId']: '',

                    'stylenumber' => (isset($ringData['ringData']['styleNumber'])) ? $ringData['ringData']['styleNumber'] : '',

                    'metaltype' => (isset($ringData['ringData']['metalType'])) ? $ringData['ringData']['metalType'] : '',

                    'centerStoneMinCarat' => (isset($ringData['ringData']['centerStoneMinCarat'])) ? $ringData['ringData']['centerStoneMinCarat'] : '',

                    'centerStoneMaxCarat' => (isset($ringData['ringData']['centerStoneMaxCarat'])) ? $ringData['ringData']['centerStoneMaxCarat'] : '',

                    'price' => (isset($ringData['ringData']['cost'])) ? $ringData['ringData']['currencySymbol'].' '.$ringData['ringData']['cost'] : '',

                    'retailerName' => (isset($ringData['ringData']['retailerInfo']->retailerName)) ? $ringData['ringData']['retailerInfo']->retailerName : '',

                    'retailerID' => (isset($ringData['ringData']['retailerInfo']->retailerID)) ? $ringData['ringData']['retailerInfo']->retailerID : '',

                    'retailerEmail' => (isset($ringData['ringData']['retailerInfo']->retailerEmail)) ? $ringData['ringData']['retailerInfo']->retailerEmail : '',

                    'retailerContactNo' => (isset($ringData['ringData']['retailerInfo']->retailerContactNo)) ? $ringData['ringData']['retailerInfo']->retailerContactNo : '',

                    'retailerFax' => (isset($ringData['ringData']['retailerInfo']->retailerFax)) ? $ringData['ringData']['retailerInfo']->retailerFax : '',

                    'retailerAddress' => (isset($ringData['ringData']['retailerInfo']->retailerAddress)) ? $ringData['ringData']['retailerInfo']->retailerAddress : '',

                );

                $this->inlineTranslation->suspend();

                $transport_admin_template = $this->_helper->getEmailTemplate('gemfindringbuilder/email/ringschedule_view_email_template_admin');

                if($transport_admin_template=='')

                    $transport_admin_template = 'gemfindringbuilder_email_ringschedule_view_email_template_admin';

                $transport = $this->_transportBuilder->setTemplateIdentifier($transport_admin_template)

                        ->setTemplateOptions($templateOptions)

                        ->setTemplateVars($templateVars)

                        ->setFrom($this->_helper->getEmailSender())

                        ->addTo($to)

                        ->getTransport();

                $transport->sendMessage();
				
				    $retaileremail = str_replace(',', ';', $retaileremail);
                    $to = $retaileremail;
				 $transport = $this->_transportBuilder->setTemplateIdentifier($transport_admin_template)

                        ->setTemplateOptions($templateOptions)

                        ->setTemplateVars($templateVars)

                        ->setFrom($this->_helper->getEmailSender())

                        ->addTo($to)

                        ->getTransport();

                $transport->sendMessage();
				
                $this->inlineTranslation->resume();

                $this->inlineTranslation->resume();

                $message = __('Thanks for your submission.');

                $data = array('status' => 1, 'msg' => $message );

                $result->setData(['output' => $data]);

                return $result;  

            } catch (\Magento\Framework\Exception\LocalizedException $e) {

                $message =  $e->getMessage();

            } catch (\RuntimeException $e) {

                $message =  $e->getMessage();

            } catch (\Exception $e) {

                $message =  __('Something went wrong. Please try again later!');

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
	
	public function sendCustomerMail($data){
		
		 if ($data) {
            try {

                $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId());

                $ringData = $this->_helper->getRingById($data['settingid']);

                $retaileremail = $ringData['ringData']['retailerInfo']->retailerEmail;

                $to = $data['email'];
				

                $templateVars = array(

                    'name' => $data['name'],

                    'email' => $data['email'],

                    'phone' => $data['phone'],                   

                    'hint_message' => $data['hint_message'],

                    'location' => $data['location'],

                    'avail_date' => $data['avail_date'],

                    'appnt_time' => $data['appnt_time'],

                    'ring_url' => (isset($data['ringurl'])) ? $data['ringurl'] : '',

                    'setting_id' => (isset($ringData['ringData']['settingId'])) ? $ringData['ringData']['settingId']: '',

                    'stylenumber' => (isset($ringData['ringData']['styleNumber'])) ? $ringData['ringData']['styleNumber'] : '',

                    'metaltype' => (isset($ringData['ringData']['metalType'])) ? $ringData['ringData']['metalType'] : '',

                    'centerStoneMinCarat' => (isset($ringData['ringData']['centerStoneMinCarat'])) ? $ringData['ringData']['centerStoneMinCarat'] : '',

                    'centerStoneMaxCarat' => (isset($ringData['ringData']['centerStoneMaxCarat'])) ? $ringData['ringData']['centerStoneMaxCarat'] : '',

                    'price' => (isset($ringData['ringData']['cost'])) ? $ringData['ringData']['currencySymbol'].' '.$ringData['ringData']['cost'] : '',

                    'retailerName' => (isset($ringData['ringData']['retailerInfo']->retailerName)) ? $ringData['ringData']['retailerInfo']->retailerName : '',

                    'retailerID' => (isset($ringData['ringData']['retailerInfo']->retailerID)) ? $ringData['ringData']['retailerInfo']->retailerID : '',

                    'retailerEmail' => (isset($ringData['ringData']['retailerInfo']->retailerEmail)) ? $ringData['ringData']['retailerInfo']->retailerEmail : '',

                    'retailerContactNo' => (isset($ringData['ringData']['retailerInfo']->retailerContactNo)) ? $ringData['ringData']['retailerInfo']->retailerContactNo : '',

                    'retailerFax' => (isset($ringData['ringData']['retailerInfo']->retailerFax)) ? $ringData['ringData']['retailerInfo']->retailerFax : '',

                    'retailerAddress' => (isset($ringData['ringData']['retailerInfo']->retailerAddress)) ? $ringData['ringData']['retailerInfo']->retailerAddress : '',

                );

                $this->inlineTranslation->suspend();

                $transport_admin_template = $this->_helper->getEmailTemplate('gemfindringbuilder/email/ringschedule_view_email_template_customer');

                if($transport_admin_template=='')

                    $transport_admin_template = 'gemfindringbuilder_email_ringschedule_view_email_template_customer';

                $transport = $this->_transportBuilder->setTemplateIdentifier($transport_admin_template)

                        ->setTemplateOptions($templateOptions)

                        ->setTemplateVars($templateVars)

                        ->setFrom($this->_helper->getEmailSender())

                        ->addTo($to)

                        ->getTransport();

                $transport->sendMessage();

                $this->inlineTranslation->resume();

                $this->inlineTranslation->resume();

                

            } catch (\Magento\Framework\Exception\LocalizedException $e) {

                $message =  $e->getMessage();

            } catch (\RuntimeException $e) {

                $message =  $e->getMessage();

            } catch (\Exception $e) {

                $message =  __('Something went wrong. Please try again later!');

            }


        }
	
	}



}

