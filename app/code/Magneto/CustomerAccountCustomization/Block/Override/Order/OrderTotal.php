<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magneto\CustomerAccountCustomization\Block\Override\Order;

use Magento\Sales\Model\Order;

class OrderTotal extends \Magento\Sales\Block\Order\Totals {

	protected function _initTotals() {
		parent::_initTotals();
		$this->removeTotal('base_grandtotal');
		return $this;
	}
}
