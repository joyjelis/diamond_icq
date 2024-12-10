<?php
namespace Travash\Education\Model\ResourceModel\Education;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            'Travash\Education\Model\Education',
            'Travash\Education\Model\ResourceModel\Education'
        );
    }
}
