<?php

namespace Magneto\UndoCartAction\Helper;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magneto\UndoCartAction\Model\Session;

class Data extends AbstractHelper {

	CONST XML_IS_ENABLE = "undocart_action/general/enabled";

	CONST XML_EXCLUDE_MINICART = "undocart_action/general/exclude_minicart";

	CONST XML_TIMEOUT = "undocart_action/general/undo_seconds";

	protected $checkoutSession;
	protected $session;
	protected $undoitems = [];

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		CheckoutSession $checkoutSession,
		Session $session
	) {
		$this->checkoutSession = $checkoutSession;
		$this->session = $session;
		parent::__construct($context);
	}

	public function excludeMinicart() {
		return $this->getConfig(self::XML_EXCLUDE_MINICART);
	}

	public function isEnabled() {
		return $this->getConfig(self::XML_IS_ENABLE);
	}

	public function getTimeout() {
		return $this->getConfig(self::XML_TIMEOUT);
	}

	public function getSetting() {
		$setting = array(
			'is_enable' => $this->isEnabled(),
			'timeout' => $this->getTimeout(),
		);

		return $setting;
	}

	/**
	 * Get Config values
	 *
	 * @param string $config
	 *
	 * return string
	 */
	public function getConfig($config) {
		return $this->scopeConfig->getValue(
			$config,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	public function saveItems($item) {
		$this->undoitems = $this->session->getUndocart();
		$this->undoitems[$item->getData('product_id')] = array(
			'qty' => $item->getData('qty'),
			'name' => $item->getData('name'),
			'buy_request' => $item->getBuyRequest(),
		);

		$this->session->setUndocart($this->undoitems);
	}

	public function getRestoreItems() {
		$this->undoitems = $this->session->getUndocart();
		if (!is_array($this->undoitems)) {
			return [];
		}

		return $this->undoitems;
	}

	public function RemoveItemFromSession($productId) {
		$this->undoitems = $this->session->getUndocart();
		if (is_array($this->undoitems)) {
			unset($this->undoitems[$productId]);
			$this->session->setUndocart($this->undoitems);
			return true;
		}
	}
}
