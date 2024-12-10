<?php
namespace MageArray\Faq\Model\ResourceModel\Faq;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            'MageArray\Faq\Model\Faq',
            'MageArray\Faq\Model\ResourceModel\Faq'
        );
    }
}
