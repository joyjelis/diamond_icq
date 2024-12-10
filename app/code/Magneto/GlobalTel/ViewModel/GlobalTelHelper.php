<?php
namespace Magneto\GlobalTel\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class GlobalTelHelper implements ArgumentInterface {

	public function __construct(
		\Magneto\GlobalTel\Helper\Data $helper
	) {
		$this->helper = $helper;
	}

	public function isEnabled() {
		return $this->helper->isEnabledTel();
	}

	public function isFBEnable($storeId = null) {
		return $this->helper->getConfigModule('enabled_fb_msg');
	}

	public function getFBPageId($storeId = null) {
		return $this->helper->getConfigModule('page_id');
	}

	public function getFBColor($storeId = null) {
		$color = $this->helper->getConfigModule('color_option');
		if (!empty($color)) {
			$color = '#' . $color;
		} else {
			$color = '#fffff';
		}

		return $color;
	}

	public function getFBLoginText($storeId = null) {
		return $this->helper->getConfigModule('login_message');
	}

	public function getFBLogoutText($storeId = null) {
		return $this->helper->getConfigModule('logout_message');
	}
}
