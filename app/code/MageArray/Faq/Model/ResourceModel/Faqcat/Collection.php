<?php
namespace MageArray\Faq\Model\ResourceModel\Faqcat;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            'MageArray\Faq\Model\Faqcat',
            'MageArray\Faq\Model\ResourceModel\Faqcat'
        );
    }
}
