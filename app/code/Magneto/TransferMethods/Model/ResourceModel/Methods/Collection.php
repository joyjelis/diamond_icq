<?php

namespace Magneto\TransferMethods\Model\ResourceModel\Methods;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	protected $_idFieldName = 'method_id';

	protected function _construct() {
		$this->_init(
			'Magneto\TransferMethods\Model\Methods',
			'Magneto\TransferMethods\Model\ResourceModel\Methods'
		);
	}
}