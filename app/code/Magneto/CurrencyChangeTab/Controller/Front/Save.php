<?php
namespace Magneto\CurrencyChangeTab\Controller\Front;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Save extends Action
{
    protected $resultPageFactory;
    private $customerRepository;
    private $currentCustomerSession;
    private $dataHelper;
    /* @phpstan-ignore-next-line */
    protected $_redirect;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $currentCustomerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magneto\CurrencyChangeTab\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $currentCustomerSession,
        CustomerRepositoryInterface $customerRepository,
        \Magneto\CurrencyChangeTab\Helper\Data $dataHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerRepository = $customerRepository;
        $this->currentCustomerSession = $currentCustomerSession;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->currentCustomerSession->isLoggedIn()) {
            $customerId = $this->currentCustomerSession->getCustomer()->getId();
            $customer = $this->customerRepository->getById($customerId);
            try {
                /* @phpstan-ignore-next-line */
                $data = (array)$this->getRequest()->getPost();
                if ($data) {
                    $preferredCurrency = $data['preferred_currency'];
                    $customer->setCustomAttribute('preferred_currency', $preferredCurrency);
                    $this->customerRepository->save($customer);
                    $this->dataHelper->updateCurrentCurrency($preferredCurrency);
                    $this->messageManager->addSuccessMessage(__("Currency Change Successfully."));
                    $this->dataHelper->refrehCache();
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e, __("We can\'t submit your request, Please try again."));
            }
        } else {
            $this->messageManager->addErrorMessage(__("We can\'t submit your request, Please try again."));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        /* @phpstan-ignore-next-line */
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
