<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_WireTransfer
 */
namespace Magneto\WireTransfer\Model;

use Magento\Framework\DataObject;

/**
 * Wire Transfer payment method model
 *
 * @method \Magento\Quote\Api\Data\PaymentMethodExtensionInterface getExtensionAttributes()
 *
 * @api
 */
class Payment extends \Magento\Payment\Model\Method\AbstractMethod {

	const CODE = 'wire_transfer';

	/**
	 * Payment method code
	 *
	 * @var string
	 */
	protected $_code = self::CODE;

	/**
	 * Availability option
	 *
	 * @var bool
	 */
	protected $_isOffline = true;

	/**
	 * Get Min Amount text from config
	 *
	 * @return string
	 */
	public function getMinAmount() {
		return trim($this->getConfigData('min_order_total'));
	}

	/**
	 * Get Max Amount from config
	 *
	 * @return string
	 */
	public function getMaxAmount() {
		return trim($this->getConfigData('max_order_total'));
	}

	public function CheckCondition($grandTotal, $minAmount, $maxAmount) {
		$isAvailable = 1;
		if ($minAmount != "") {
			if ($minAmount > $grandTotal) {
				$isAvailable = 0;
			}
		}

		if ($maxAmount != "") {
			if ($maxAmount < $grandTotal) {
				$isAvailable = 0;
			}
		}

		return $isAvailable;
	}

	public function getInstructions() {
		return trim($this->getConfigData('instructions'));
	}

	/**
	 * Check whether payment method can be used
	 *
	 * @param \Magento\Quote\Api\Data\CartInterface|null $quote
	 * @return bool
	 */
	public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null) {
		if (!$this->isActive($quote ? $quote->getStoreId() : null)) {
			return false;
		}

		if ($quote) {
			$grandTotal = $quote->getGrandTotal();
			$minAmount = $this->getMinAmount();
			$maxAmount = $this->getMaxAmount();

			$isAvailable = $this->CheckCondition($grandTotal, $minAmount, $maxAmount);

			if ($isAvailable == 0) {
				return false;
			}
		}

		$checkResult = new DataObject();
		$checkResult->setData('is_available', true);

		// for future use in observers
		$this->_eventManager->dispatch(
			'payment_method_is_active',
			[
				'result' => $checkResult,
				'method_instance' => $this,
				'quote' => $quote,
			]
		);

		return $checkResult->getData('is_available');
	}
}
