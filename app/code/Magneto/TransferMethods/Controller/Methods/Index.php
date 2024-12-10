<?php

namespace Magneto\TransferMethods\Controller\Methods;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action {
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
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Model\Url $customerUrl,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
		$this->_customerSession = $customerSession;
		$this->_customerUrl = $customerUrl;
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}

	/**
	 * Dispatch function
	 *
	 * @param RequestInterface $request
	 * @return void
	 */
	public function dispatch(RequestInterface $request) {
		$loginUrl = $this->_customerUrl->getLoginUrl();
		if (!$this->_customerSession->authenticate($loginUrl)) {
			$this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
		}
		return parent::dispatch($request);
	}

	/**
	 * Execute view action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$resultPage = $this->resultPageFactory->create();
		return $resultPage;
	}
}
