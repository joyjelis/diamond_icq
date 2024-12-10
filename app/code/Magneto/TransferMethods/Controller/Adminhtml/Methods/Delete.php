<?php

declare (strict_types = 1);

namespace Magneto\TransferMethods\Controller\Adminhtml\Methods;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magneto\TransferMethods\Helper\Data;
use Magneto\TransferMethods\Model\MethodsFactory;


class Delete extends \Magento\Backend\App\Action {
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;

	protected $helper;

	protected $collectionFactory;

	/**
	 * Constructor
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		Context $context,
		Data $helper,
		PageFactory $resultPageFactory,
		MethodsFactory $collection
	) {
		$this->helper = $helper;
		$this->resultPageFactory = $resultPageFactory;
		$this->collectionFactory = $collection;
		parent::__construct($context);
	}

	/**
	 * Delete action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$resultRedirect = $this->resultRedirectFactory->create();
		$id = $this->getRequest()->getParam('method_id');
		if ($id) {
			try {
				$model = $this->collectionFactory->create()->load($id);
                $model->delete();
				$this->messageManager->addSuccessMessage(__('Method successfully deleteded.'));
				return $resultRedirect->setPath('*/*/');
			} catch (\Exception $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
				return $resultRedirect->setPath('*/*/edit', ['method_id' => $id]);
			}
		}
		$this->messageManager->addErrorMessage(__('We can\'t find a method to delete.'));
		return $resultRedirect->setPath('*/*/');
	}
}
