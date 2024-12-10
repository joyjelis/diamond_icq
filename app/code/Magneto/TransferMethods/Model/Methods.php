<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_TransferMethods
 */

namespace Magneto\TransferMethods\Model;

use Magento\Framework\Model\AbstractModel;

class Methods extends AbstractModel {

	protected function _construct() {
		$this->_init('Magneto\TransferMethods\Model\ResourceModel\Methods');
	}
}