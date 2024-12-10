<?php declare (strict_types = 1);

namespace Magneto\TransferMethods\Controller\Adminhtml\Methods;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magneto\TransferMethods\Helper\Data;

class save extends \Magento\Backend\App\Action {
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;

	protected $is_ajax = false;

	/**
	 * Constructor
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		Context $context,
		Data $helper,
		PageFactory $resultPageFactory,
		JsonFactory $resultJsonFactory
	) {
		$this->helper = $helper;
		$this->resultPageFactory = $resultPageFactory;
		$this->resultJsonFactory = $resultJsonFactory;
		parent::__construct($context);
	}

	/**
	 * Execute view action
	 *
	 * @return ResultInterface
	 */
	public function execute() {
		$post = $this->getRequest()->getPost();
		try {
			if (isset($post['method_id']) && !empty($post['method_id'])) {
				$id = $post['method_id'];
				$success = $this->helper->SaveDataAdmin($post, $id);
				$msg = __('Transfer Method Updated.');
			} else {
				unset($post['method_id']);
				$success = $this->helper->SaveData($post);
				$msg = __('Transfer Method Added.');
			}

			if ($this->getRequest()->isXmlHttpRequest()) {
				$this->is_ajax = true;
			}

			if ($success) {
				$this->messageManager->addSuccessMessage($msg);
				if ($this->is_ajax) {
					$result = $this->resultJsonFactory->create();
					return $result->setData(['success' => true]);
				}

				$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

				if ($this->getRequest()->getParam('back')) {
	                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
	            } else {
	                $resultRedirect->setPath('transfer/methods');
	            }
				return $resultRedirect;

			}

		} catch (\Exception $e) {
			$this->messageManager->addErrorMessage(__($e->getMessage()));
		}

		if ($this->is_ajax) {
			$result = $this->resultJsonFactory->create();
			return $result->setData(['success' => false]);
		}

		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$resultRedirect->setUrl($this->_redirect->getRefererUrl());
		return $resultRedirect;
	}
}