<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Magneto\AmastyCustomization\Override\Plugin\Ajax;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Request\Http;

class Ajax extends \Amasty\Shopby\Plugin\Ajax\Ajax {

	private $customThemes = ['fcnet/blank_julbo', 'Smartwave/porto', 'Magneto/diamondicq'];

	/**
	 * @param $responseData
	 * @param $htmlCategoryData
	 * @param $layout
	 */
	private function addCategoryData($htmlCategoryData, $layout, &$responseData) {
		if (in_array($this->design->getDesignTheme()->getCode(), $this->customThemes)) {
			$responseData['image'] = $this->getBlockHtml($layout, 'category.image');
			$responseData['description'] = $this->getBlockHtml($layout, 'category_desc_main_column');
			if (empty($responseData['description'])) {
				$responseData['description'] = $this->getBlockHtml($layout, 'category.description');
			}
		} else {
			// @codingStandardsIgnoreStart
			$htmlCategoryData = '<div class="category-view">' . $htmlCategoryData . '</div>';
			// @codingStandardsIgnoreEnd
			$responseData['categoryData'] = $htmlCategoryData;
		}
	}
}