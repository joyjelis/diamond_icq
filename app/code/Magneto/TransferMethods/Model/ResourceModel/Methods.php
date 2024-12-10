<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_TransferMethods
 */

namespace Magneto\TransferMethods\Model\ResourceModel;

class Methods extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

	protected function _construct() {
		$this->_init('transfer_methods', 'method_id');
	}
}