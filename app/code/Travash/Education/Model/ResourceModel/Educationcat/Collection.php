<?php
namespace Travash\Education\Model\ResourceModel\Educationcat;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            'Travash\Education\Model\Educationcat',
            'Travash\Education\Model\ResourceModel\Educationcat'
        );
    }
}
