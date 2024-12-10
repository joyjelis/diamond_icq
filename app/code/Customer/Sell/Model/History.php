<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_Quote
 */

namespace Customer\Sell\Model;

use Magento\Framework\Model\AbstractModel;

class History extends AbstractModel
{

    protected function _construct()
    {
        $this->_init('Customer\Sell\Model\ResourceModel\History');
    }
}
