<?php



/**

 * @copyright Copyright (c) 2018 www.Gemfind.com

 */



namespace Gemfind\Ringbuilder\Controller\Settings;



use Magento\Framework\App\Action\Action;

use Magento\Framework\App\Action\Context;

use Magento\Framework\Controller\Result\JsonFactory;



class Completedrophint extends Action {





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

     * ResultDropHint constructor.

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

                $ringData = $this->_helper->getRingById($data['settingid']);

                $retaileremail = $ringData['ringData']['retailerInfo']->retailerEmail;

                if($this->_helper->getAdminEmail()){
                    $to = $this->_helper->getAdminEmail();
                } else {
                    $retaileremail = str_replace(',', ';', $retaileremail);
                    $to = $retaileremail;
                }
                
                $retailername = $ringData['ringData']['retailerInfo']->retailerName;

                $templateVars = array(

                    'retailername' => $retailername,

                    'retailerphone' => $ringData['ringData']['retailerInfo']->retailerContactNo,

                    'name' => $data['name'],

                    'email' => $data['email'],

                    'recipient_name' => $data['recipient_name'],

                    'recipient_email' => $data['recipient_email'],

                    'gift_reason' => $data['gift_reason'],

                    'hint_message' => $data['hint_message'],

                    'gift_deadline' => $data['gift_deadline'],

                    'ring_url' => $data['ringurl'],

                    'not_view_item' => 1,

                );

                $this->inlineTranslation->suspend();
                

                $transport_sender_template = $this->_helper->getEmailTemplate('gemfindringbuilder/email/ringdrop_hint_email_template_sender');

                if($transport_sender_template=='')

                    $transport_sender_template = 'gemfindringbuilder_email_ringdrop_hint_email_template_sender';


                $transport_sender = $this->_transportBuilder->setTemplateIdentifier($transport_sender_template)

                        ->setTemplateOptions($templateOptions)

                        ->setTemplateVars($templateVars)

                        ->setFrom($this->_helper->getEmailSender())

                        ->addTo($data['email'])

                        ->getTransport();

                $transport_sender->sendMessage();         

                $transport_receiver_template = $this->_helper->getEmailTemplate('gemfindringbuilder/email/ringdrop_hint_email_template_receiver');

                if($transport_receiver_template=='')

                    $transport_receiver_template = 'gemfindringbuilder_email_ringdrop_hint_email_template_receiver';

                $transport_receiver = $this->_transportBuilder->setTemplateIdentifier($transport_receiver_template)

                        ->setTemplateOptions($templateOptions)

                        ->setTemplateVars($templateVars)

                        ->setFrom($this->_helper->getEmailSender())

                        ->addTo($data['recipient_email'])

                        ->getTransport();

                $transport_receiver->sendMessage();

                $transport_retailer_template = $this->_helper->getEmailTemplate('gemfindringbuilder/email/ringdrop_hint_email_template_retailer');

                if($transport_retailer_template=='')

                    $transport_retailer_template = 'gemfindringbuilder_email_ringdrop_hint_email_template_retailer';

                $transport_retailer = $this->_transportBuilder->setTemplateIdentifier($transport_retailer_template)

                        ->setTemplateOptions($templateOptions)

                        ->setTemplateVars($templateVars)

                        ->setFrom($this->_helper->getEmailSender())

                        ->addTo($to)

                        ->getTransport();

                $transport_retailer->sendMessage();

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

