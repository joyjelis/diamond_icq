<?php
namespace Magneto\AmastyCustomization\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class CategoryHelper implements ArgumentInterface {

	public function __construct(
		\Magneto\AmastyCustomization\Helper\Data $helper
	) {
		$this->helper = $helper;
	}

	public function getAllSubCategoryDetails($categoryId) {
		return $this->helper->getAllSubCategoryDetails($categoryId);
	}
}
