<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package UndoCartAction
 */

namespace Magneto\UndoCartAction\Plugin;

use Magento\Quote\Model\Quote;

class SaveItem {
	/**
	 * @var Helper
	 */
	private $helper;

	public function __construct(
		\Magneto\UndoCartAction\Helper\Data $helper,
		\Magento\Framework\App\RequestInterface $request
	) {
		$this->_request = $request;
		$this->helper = $helper;
	}

	public function aroundRemoveItem(Quote $subject, $proceed, $itemId) {
		if ($this->helper->isEnabled()) {
			$excludeminicart = $this->helper->excludeMinicart();
			if ($excludeminicart == 0) {
				if ($this->_request->getPathInfo() == "/checkout/sidebar/removeItem/") {
					return $proceed($itemId);
				}
			}

			$item = $subject->getItemById($itemId);

			try {
				$return = $proceed($itemId);
				$this->helper->saveItems($item);
			} catch (\Exception $e) {
				// do nothing if error occured
			}
		}

		return $return;
	}
}
