<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package UndoCartAction
 */

namespace Magneto\UndoCartAction\Controller\Cart;

use Magneto\UndoCartAction\Helper\Data;

class RemoveItems extends \Magento\Framework\App\Action\Action {

	protected $resultJsonFactory;

	protected $regionColFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		Data $helper
	) {
		$this->resultJsonFactory = $resultJsonFactory;
		$this->helper = $helper;
		parent::__construct($context);
	}

	public function execute() {
		$this->_view->loadLayout();
		$this->_view->getLayout()->initMessages();
		$this->_view->renderLayout();

		$result = $this->resultJsonFactory->create();
		$remove_id = $this->getRequest()->getPost('remove_id');
		$this->helper->RemoveItemFromSession($remove_id);
		return $result->setData(['success' => true]);
	}
}