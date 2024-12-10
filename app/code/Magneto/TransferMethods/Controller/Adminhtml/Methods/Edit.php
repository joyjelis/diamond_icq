<?php

namespace Magneto\TransferMethods\Controller\Adminhtml\Methods;

class Edit extends \Magento\Backend\App\Action {

	protected $_pageFactory;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory
	) {
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute() {
		$method_id = $this->getRequest()->getParam('method_id');
		$resultPage = $this->_pageFactory->create();
	
		$title = __('Add Transfer Method');
		if ($method_id) {
			$title = __('Edit Transfer Method');	
        }

		$resultPage->getConfig()->getTitle()->prepend(__($title));
		return $resultPage;
	}
}
