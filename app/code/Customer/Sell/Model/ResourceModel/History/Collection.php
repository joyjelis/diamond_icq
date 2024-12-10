<?php

namespace Customer\Sell\Model\ResourceModel\History;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'sell_history_id';

    protected function _construct()
    {
        $this->_init(
            'Customer\Sell\Model\History',
            'Customer\Sell\Model\ResourceModel\History'
        );
    }
}
