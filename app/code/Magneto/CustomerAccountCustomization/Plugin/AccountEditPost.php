<?php
	
	namespace Magneto\CustomerAccountCustomization\Plugin;
	use Magento\Store\Model\StoreManagerInterface;
	class AccountEditPost
	{	protected $logger;
		protected $customer;
		protected $customerRepository;
		protected $storeManager;
		private $messageManager;

		public function __construct(
	        \Psr\Log\LoggerInterface $logger,
	        StoreManagerInterface $storeManager,
	        \Magento\Customer\Model\Session $customer,
	        \Magento\Customer\Model\CustomerFactory $customerFactory,
	        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
	        \Magento\Framework\Message\ManagerInterface $messageManager
	    ){
	        $this->logger = $logger;
	        $this->storeManager = $storeManager;
	        $this->customer = $customer;
	        $this->customerRepository = $customerRepository;
	        $this->customerFactory = $customerFactory;
	        $this->messageManager = $messageManager;
	    }

		public function afterExecute(\Magento\Customer\Controller\Account\EditPost $subject, $result) {
			try{
				$email = $subject->getRequest()->getParam('email');
				$customerId = $this->customer->getId();
				$customerObj = $this->customerFactory->create()->load($customerId);
			    if($customerObj->getId()){
				    $customerObj->setData('email',$email)->save();
			    }
			    
			}catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
			    $message = __('There is already an account with this emails address.');
			     $this->messageManager->addErrorMessage($message);

			} catch (\Exception $e) {
                 $message = __("We can't save the customer.");
			     $this->messageManager->addErrorMessage($message);

            }
			
		    return $result;
		}
	}