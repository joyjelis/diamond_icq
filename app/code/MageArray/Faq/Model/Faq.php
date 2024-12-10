<?php
namespace MageArray\Faq\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Faq
 * @package MageArray\Faq\Model
 */
class Faq extends AbstractModel
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('MageArray\Faq\Model\ResourceModel\Faq');
    }
}
