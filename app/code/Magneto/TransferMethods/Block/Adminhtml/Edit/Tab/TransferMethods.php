<?php

namespace Magneto\TransferMethods\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class TransferMethods extends \Magento\Backend\Block\Template implements TabInterface {

	protected $_coreRegistry;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		array $data = []
	) {
		$this->_coreRegistry = $registry;
		parent::__construct($context, $data);
	}

	public function getTabLabel() {
		return __('Transfer Methods');
	}

	public function getTabTitle() {
		return __('Transfer Methods');
	}

	public function canShowTab() {
		if ($this->getCustomerId()) {
			return true;
		}
		return false;
	}

	public function getCustomerId() {
		return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
	}

	public function isHidden() {
		if ($this->getCustomerId()) {
			return false;
		}
		return true;
	}

	public function getTabClass() {
		return '';
	}

	public function getTabUrl() {
		return $this->getUrl('transfer/methods/load', ['_current' => true]);
	}

	public function isAjaxLoaded() {
		return true;
	}
}