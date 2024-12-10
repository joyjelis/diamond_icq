<?php

namespace Customer\Sell\Model\ResourceModel\Sell;

use Customer\Sell\Model\Sell as Model;
use Customer\Sell\Model\ResourceModel\Sell as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'customer_sell_customer_sell_collection';
    protected $_eventObject = 'customer_sell_collection';

    /**
     * Define the resource model & the model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
