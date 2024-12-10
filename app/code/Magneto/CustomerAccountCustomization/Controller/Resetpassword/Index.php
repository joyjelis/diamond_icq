<?php

namespace Magneto\CustomerAccountCustomization\Controller\Resetpassword;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\SessionCleanerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\Redirect;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $customerAccountManagement;
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        ?SessionCleanerInterface $sessionCleaner = null
    ) {
        parent::__construct($context);
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->sessionCleaner = $sessionCleaner ?: ObjectManager::getInstance()->get(SessionCleanerInterface::class);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $isPasswordChanged = false;
        $customerData = $this->customerRepository->getById($this->session->getCustomerId());
        $email = $customerData->getEmail();
        if ($this->getRequest()->getParam('change_my_password')) {
            $currPass = $this->getRequest()->getPost('current_password');
            $newPass = $this->getRequest()->getPost('password');
            $confPass = $this->getRequest()->getPost('password_confirmation');
            if ($newPass != $confPass) {
                throw new InputException(__('Password confirmation doesn\'t match entered password.'));
            }
            $isPasswordChanged = $this->customerAccountManagement->changePassword($email, $currPass, $newPass);
        }
        if ($isPasswordChanged) {
            $this->session->logout();
            $this->session->start();
            $this->messageManager->addSuccessMessage(__('Your password has been reset'));
            return $resultRedirect->setPath('customer/account/login');
        } else {
            $this->messageManager->addErrorMessage(__('You password has not been reset. please try again.'));
            return $resultRedirect->setPath('customer/account');
        }
    }
}
