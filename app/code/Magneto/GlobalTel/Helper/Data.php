<?php

namespace Magneto\GlobalTel\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	CONST Config = 'm_utils/general/';

	/**
	 * @var string
	 */
	protected $pageConfig;

	/**
	 * @var array
	 */
	protected $configModule;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\View\Page\Config $pageConfig
	) {
		parent::__construct($context);
		$this->pageConfig = $pageConfig;
	}

	public function isEnabledTel() {
		return $this->scopeConfig->getValue(self::Config . "enabled", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function getConfigModule($cfg = '', $value = null) {
		if ($cfg) {
			return $this->scopeConfig->getValue(self::Config . $cfg, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		}
	}

	public function addBodyClass($class) {
		$this->pageConfig->addBodyClass($class);
	}

	/**
	 * @return string
	 */
	public function getPageLayout() {
		return $this->pageConfig->getPageLayout();
	}

}