<?php
namespace Magneto\CustomerAccountCustomization\Helper;

use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**

     * @var LoggerInterface

     */

    protected $logger;
    /**

     * @var \Magento\Store\Model\StoreManagerInterface

     */

    public $_storeManager;
    protected $customer;
    /**

     * Data constructor.

     * @param Context $context

     * @param LoggerInterface $logger

     * @param \Magento\Store\Model\StoreManagerInterface $storeManager

     */

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customer,
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress,
        \Magento\Customer\Model\Customer $customerModel
    ) {
        $this->logger = $logger;
        $this->_storeManager=$storeManager;
        $this->customer = $customer;
        $this->customerModel = $customerModel;
        $this->currentCustomerAddress = $currentCustomerAddress;
        parent::__construct($context);
    }

    public function getCustomerPhone()
    {
        $phoneNumber = '';
        $customer = $this->customer;
        $customerId = $customer->getId();
        $customerObj = $this->customerModel->load($customerId);
        $phoneNumber = $customerObj->getPhoneNumber();
        $addresses = $customerObj->getAddresses();
        if (empty($phoneNumber) && count($addresses)>0) {
            foreach ($addresses as $address) {
                $customerData = $address->getData();
                $phoneNumber = $customerData['telephone'];
                break;
            }
        }
        return preg_replace('/[^0-9+]/', '', $phoneNumber);
    }
}
