<?php

namespace Magneto\UndoCartAction\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class UndoItem extends Template {

	protected $helper;
	protected $jsconfig;
	public function __construct(
		Context $context,
		\Magneto\UndoCartAction\Helper\Data $helper,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->jsconfig['restore'] = 0;
		$this->helper = $helper;
		$this->initJsConfig();
	}

	public function getTimeout() {
		return (int) $this->helper->getTimeout();
	}

	public function getConfig() {
		$items = $this->helper->getRestoreItems();
		foreach ($items as $productId => $info) {
			$this->jsconfig['products_ids'][$productId] = array(
				'productId' => $productId,
				'info' => $info,
			);
		}

		if (isset($this->jsconfig['products_ids']) && !empty($this->jsconfig['products_ids'])) {
			$this->jsconfig['restore'] = 1;
		}

		return json_encode($this->jsconfig);
	}

	protected function initJsConfig() {
		$this->jsconfig['button_label'] = __("Restore Item");
		$this->jsconfig['setting'] = $this->helper->getSetting();
		$this->jsconfig['remove_url'] = $this->getUrl('restoreitem/cart/removeitems', ['restore_id' => "RESTORE_ID"]);
		$this->jsconfig['restore_url'] = $this->getUrl('restoreitem/cart/restoreitem', ['restore_id' => "RESTORE_ID"]);
	}
}
