<?php

namespace Magneto\CustomProductDetails\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomProductDetailsHelper implements ArgumentInterface {

	public function __construct(
		\Magneto\CustomProductDetails\Helper\Data $helper
	) {
		$this->helper = $helper;
	}
	public function getProductDeliveryDate($product) {
		return $this->helper->getProductDeliveryDate($product);
	}
	public function getOrderByTime() {
		return $this->helper->getOrderByTime();
	}
	public function getShippingMethod() {
		return $this->helper->getShippingMethod();
	}
	public function getProductPrice($product) {
		return $this->helper->getProductPrice($product);
	}
	public function getProductAttributeGroupsDetail($product) {
		return $this->helper->getProductAttributeGroupsDetail($product);
	}
}