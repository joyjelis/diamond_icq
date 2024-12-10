<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_AjaxProductsLoad
 */

namespace Magneto\AjaxProductsLoad\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magneto\AjaxProductsLoad\Helper\Data;

class Products extends Template {
	/**
	 * Block Template
	 *
	 * @var string
	 */
	protected $_template = "Magneto_AjaxProductsLoad::loadproducts.phtml";

	/**
	 * Helper
	 *
	 * @var Magneto\AjaxProductsLoad\Helper\Data
	 */
	protected $helper;

	/**
	 * JSCONFIG
	 *
	 * @var array
	 */
	protected $jsconfig;

	/**
	 * Manage Sub category
	 *
	 * @var array
	 */
	protected $managesub;

	/**
	 * Category
	 *
	 * @var Magento\Category\Model\Category
	 */
	protected $category;

	/**
	 *  _Construct
	 *
	 * @param Context $context
	 * @param Data $helper
	 * @param array $data
	 */
	public function __construct(
		Context $context,
		Data $helper,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->helper = $helper;
		$this->InitCategory();
	}

	public function ManageSubCat() {
		$use_subcateory = 1;
		if ($this->getData('use_subcateory') == 0) {
			$use_subcateory = $this->getData('use_subcateory');
		}

		if ($use_subcateory == 1) {
			$this->managesub['filter_from_sub'] = 1;
			$this->managesub['categoryId'] = $this->getData('categoryId');
		} else {
			$this->managesub['filter_from_sub'] = 0;
			$this->managesub['categoryIds'] = $this->getData('categoryIds');
			$this->managesub['title'] = $this->getData('title');
		}
	}

	/**
	 * Init Category
	 *
	 * @return null
	 */
	public function InitCategory() {
		$this->ManageSubCat();
		$filtercategory = $this->getData('filter_category');
		$is_enabled = $this->helper->isEnabled();

		if ($is_enabled == 1) {
			if ($this->managesub['filter_from_sub'] == 1) {
				$this->category = $this->helper->getCategory($this->managesub['categoryId']);
				if (!empty($this->category)) {
					$this->jsconfig['category_name'] = $this->category->getName();
					$this->jsconfig['sidebarcategories'] = $this->helper->getSidebarCategories($this->category);
				} else {
					$this->jsconfig['category_name'] = null;
					$this->jsconfig['sidebarcategories'] = null;
				}
			} else if ($this->managesub['filter_from_sub'] == 0) {
				$this->jsconfig['category_name'] = __($this->managesub['title']);
				$this->jsconfig['sidebarcategories'] = $this->helper->getSidebarCategories($this->managesub['categoryIds']);
			}

			$this->jsconfig['filtercategories'] = $this->helper->getFilterCategories($filtercategory);
			$categoryIds = $this->getCategoryIds($this->jsconfig['sidebarcategories'], $this->jsconfig['filtercategories']);
			$this->jsconfig['products'] = $this->helper->getProducts($categoryIds, $this->helper->getDesktopLimit());
			$this->jsconfig['products_size'] = $this->helper->getCollectionSize();
			$this->jsconfig['products_mobile'] = $this->helper->getProducts($categoryIds, $this->helper->getMobileLimit());
			$this->jsconfig['products_mobile_size'] = $this->helper->getCollectionSize();
			$this->jsconfig['callbackurl'] = $this->callbackurl();
		} else {
			$this->jsconfig['category_name'] = null;
			$this->jsconfig['sidebarcategories'] = null;
			$this->jsconfig['filtercategories'] = null;
			$this->jsconfig['products'] = null;
			$this->jsconfig['callbackurl'] = $this->callbackurl();
		}
	}

	/**
	 * Check Module Enabled
	 *
	 * @return boolean
	 */
	public function isEnabled() {
		return $this->helper->isEnabled();
	}

	/**
	 * Get Categories
	 *
	 * @param array $activeSidebar
	 * @param array $activeFilters
	 * @return array
	 */
	public function getCategoryIds($activeSidebar, $activeFilters) {
		$categoryIds = array();
		if (isset($activeSidebar['active']) && !empty($activeSidebar['active'])) {
			array_push($categoryIds, $activeSidebar['active']);
		}

		if (isset($activeFilters['active']) && !empty($activeFilters['active'])) {
			array_push($categoryIds, $activeFilters['active']);
		}

		return $categoryIds;
	}

	/**
	 * Block Config
	 *
	 * @return json
	 */
	public function getJsConfig() {
		$this->jsconfig['desktop_limit'] = $this->helper->getDesktopLimit();
		$this->jsconfig['mobile_limit'] = $this->helper->getMobileLimit();
		return json_encode($this->jsconfig);
	}

	/**
	 * Callback Url
	 *
	 * @return string
	 */
	public function callbackurl() {
		return $this->getUrl('loadsection/product/index');
	}
}
