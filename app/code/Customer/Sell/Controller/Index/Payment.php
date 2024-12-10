<?php

namespace Customer\Sell\Controller\Index;

use Customer\Sell\Model\Sell;
use Customer\Sell\Model\SellFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Payment extends \Magento\Framework\App\Action\Action
{

    /**
     * @var SellFactory
     */
    private $sellFactory;

    /**
     * Payment constructor
     *
     * @param Context $context
     * @param SellFactory $sellFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        SellFactory $sellFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->sellFactory = $sellFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->_request->getParam('id');
        $return_url = $this->_request->getParam('return_url');
        $sell = $this->sellFactory->create()->load($id);
        $sell->setStatus(Sell::CUSTOMER_ASKED_FOR_RETURN_SHIPPING_PAYMENT_LINK_STATUS);
        $sell->save();
        $sell = $this->sellFactory->create()->load($id);
        $this->_eventManager->dispatch(
            'sell_diamond_email',
            ['sell' => $sell]
        );

        $this->messageManager->addSuccessMessage(__('Your response submitted. Our team Contact you soon.'));
        if ($this->_customerSession->isLoggedIn()) {
            if ($this->_customerSession->getId() == $sell->getCustomerId()) {
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('*/quote/view', ['id' => $sell->getId()]);
                return $resultRedirect;
            }
        }
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_url->getUrl('*'));
        if ($return_url) {
            $resultRedirect->setUrl(base64_decode($return_url));
        }

        return $resultRedirect;
    }
}
