<?php

namespace Magneto\TransferMethods\Block\Adminhtml\Grid\Column;

use Magento\Framework\DataObject;

class Country extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

	public function __construct(
		\Magneto\TransferMethods\Helper\Data $helper
	) {
		$this->helper = $helper;
	}

	/**
	 * get category name
	 * @param  DataObject $row
	 * @return string
	 */
	public function render(DataObject $row) {
		return $this->helper->getCountryname($row->getCountry());
	}
}