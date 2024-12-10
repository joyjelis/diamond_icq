<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magneto\MyAccountOrderCustomization\Block\Override\Order;

use Magento\Sales\Block\Order\Recent As OriginalBlock;

class Recent extends OriginalBlock {

	/**
	 * @inheritDoc
	 */
	protected function _construct() {
		parent::_construct();
		$this->getRecentOrders();
	}

	private function getRecentOrders() {
		$customerId = $this->_customerSession->getCustomerId();
		$orders = $this->_orderCollectionFactory->create($customerId)->addAttributeToSelect(
			'*'
		)->addAttributeToFilter(
			'customer_id',
			$customerId
		)->addAttributeToFilter(
			'status',
			['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
		)->addAttributeToSort(
			'created_at',
			'desc'
		)->setPageSize(
			self::ORDER_LIMIT
		)->load();

		$this->setOrders($orders);
	}
}
