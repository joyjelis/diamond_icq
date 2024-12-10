<?php

namespace Customer\Sell\Controller\Quote;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Framework\App\Action\Action
{
    protected $_customerSession;

    protected $_customerUrl;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Customer\Sell\Model\SellFactory $sellFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerUrl = $customerUrl;
        $this->_sellFactory = $sellFactory;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_customerUrl->getLoginUrl();
        if (!$this->_customerSession->create()->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        if (!$this->IsAuth()) {
            $this->messageManager->addNoticeMessage(__('Query not found.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/customer/index');
            return $resultRedirect;
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    public function IsAuth()
    {
        $params = $this->getRequest()->getParams();
        if ($customerid = $this->_customerSession->create()->getId()) {
            $sellItems = $this->_sellFactory->create()->load($params['id']);
            if ($sellItems->getData('customer_id') == $customerid) {
                return true;
            } else {
                return false;
            }
        }
    }
}
