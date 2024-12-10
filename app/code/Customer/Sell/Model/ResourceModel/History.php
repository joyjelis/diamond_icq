<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_Quote
 */

namespace Customer\Sell\Model\ResourceModel;

class History extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('sell_quote_history', 'sell_history_id');
    }
}
