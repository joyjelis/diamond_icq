<?php
namespace Customer\Sell\Controller\Adminhtml\Sell;

use Customer\Sell\Helper\Data as QuoteHelper;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Store\Model\App\Emulation;

class Save extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Customer_Sell::save';

    protected $dataPersistor;

    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param Emulation $emulation
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        QuoteHelper $helper,
        Emulation $emulation
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->emulation = $emulation;
        $this->helper = $helper;
    }
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();
        $storeId = $this->helper->getStoreId();
        $this->emulation->startEnvironmentEmulation($storeId);
        if ($data) {
            $id = $this->getRequest()->getParam('sell_id');

            $imageList = $this->helper->ProcessImages($data);
            $data['image'] = implode(',', $imageList);
            $data = $this->helper->ProcessLabData($data);
            try {
                if ($this->helper->SaveData($id, $data)) {
                    $this->messageManager->addSuccessMessage(__('You saved the Sell.'));
                    $this->dataPersistor->clear('customer_sell_sell');
                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect->setPath('*/*/edit', ['sell_id' => $id]);
                    }
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
            }

            $this->dataPersistor->set('customer_sell_sell', $data);

            return $resultRedirect->setPath('*/*/edit', ['sell_id' => $this->getRequest()->getParam('sell_id')]);

            // $model = $this->_objectManager->create(\Customer\Sell\Model\Sell::class)->load($id);
            // if (!$model->getId() && $id) {
            //     $this->messageManager->addErrorMessage(__('This Sell no longer exists.'));
            //     return $resultRedirect->setPath('*/*/');
            // }

            try {
                $data['price'] = $this->sellViewModel->formatSellPrice($price);
                if ($data['status'] == '3') {
                    // $this->emailHelper->sendMail($data, 'sell_item_not_qualify', 'Trade Not Qualify');
                    // $this->SendEmailNotQualify($data);
                } elseif ($data['pay_shipping'] != '' && $data['status'] == '13') {
                    // $this->emailHelper->sendMail($data, 'sell_item_not_qualify', 'Trade Not Qualify');
                    // $this->emailHelper->sendMail($data, 'sell_item_not_passed_inspection', 'Trade Not Passed Inspection');
                    //$this->SendEmailNotQualify($data);
                    // $this->sendEmailInspectionnotPassed($data);
                } elseif ($data['price'] != '' && $data['certificate'] = '1' && $data['status'] == '2') {
                    $price = $data['price'];
                    $data['price'] = $this->sellViewModel->formatSellPrice($price);
                    // $this->emailHelper->sendMail($data, 'sell_item_qualify', 'Trade Qualify');
                    // $this->SendEmailQualify($data);
                }
                if ($data['inspection_pass'] = '1' && $data['status'] == '8') {
                    // $this->emailHelper->sendMail($data, 'sell_item_paid_to_customer', 'Trade Paid');
                    // $this->SendItemPaidCustomer($data);
                } elseif ($data['inspection_pass'] = '0' || $data['status'] == '12') {
                    // $this->emailHelper->sendMail($data, 'sell_item_not_passed_inspection', 'Trade Not Passed Inspection');
                }
                if ($data['offer_price'] != '' && $data['status'] == '4') {
                    $price = $data['price'];
                    $data['price'] = $this->sellViewModel->formatSellPrice($price);
                    $price = $data['offer_price'];
                    $data['offer_price'] = $this->sellViewModel->formatSellPrice($price);
                    // $this->emailHelper->sendMail($data, 'sell_item_qualify_secound_valuation', 'Trade Reconsideration');
                    // $this->SendReconsiderPrice($data);
                }
                if ($data['status'] == '5') {
                    // $this->emailHelper->sendMail($data, 'sell_item_consignment', 'Trade Consignment');
                    // $this->SendConsignments($data);
                }
                if ($data['status'] == '14' && $data['schedule_accept'] == '1') {
                    // $this->emailHelper->sendMail($data, 'sell_item_email_appoinement_accept', 'Trade Appointment Accepted');
                    // $this->SendAppoinmentAccepted($data);
                }
                if ($data['status'] == '18' && $data['schedule_accept'] == '1') {
                    // $this->emailHelper->sendMail($data, 'sell_item_email_appoinement_accept', 'Trade Appointment Accepted');
                    // $this->SendAppoinmentAccepted($data);
                }
                if ($data['status'] == '14' && $data['schedule_accept'] == '2') {
                    // $this->emailHelper->sendMail($data, 'sell_item_email_appoinement_not_accept', 'Trade Appointment Not Accepted');
                    // $this->SendAppoinmentNotAccepted($data);
                }
                if ($data['status'] == '17') {
                    $data['appointment_schedule_date'] = $this->timezone->date($data['schedule_date'])->format('l,d/m/Y');
                    $data['appointment_schedule_time'] = $this->timezone->date($data['schedule_date'])->format('g:i A');
                    $data['appointment_sent_date'] = $this->timezone->date($data['updated_at'])->format('d/m/Y');
                    // $this->emailHelper->sendMail($data, 'sell_item_email_appoinement_accept', 'Trade Appointment Schedule');
                    // $this->SendAppoinmentAdmin($data);
                }

                if ($data['status'] == '22') {
                    // $this->emailHelper->sendMail($data, 'pick_request_generated', 'Pickup Generated');
                }

                $this->emulation->stopEnvironmentEmulation();

                $this->messageManager->addSuccessMessage(__('You saved the Sell.'));
                $this->dataPersistor->clear('customer_sell_sell');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['sell_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
            }
            $this->dataPersistor->set('customer_sell_sell', $data);
            return $resultRedirect->setPath('*/*/edit', ['sell_id' => $this->getRequest()->getParam('sell_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Is the user allowed to view the page.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(static::ADMIN_RESOURCE);
    }
}
