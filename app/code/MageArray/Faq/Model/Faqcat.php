<?php
namespace MageArray\Faq\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Faqcat
 * @package MageArray\Faq\Model
 */
class Faqcat extends AbstractModel
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('MageArray\Faq\Model\ResourceModel\Faqcat');
    }

    /**
     * @param $urlKey
     * @return mixed
     */
    public function checkUrlKey($urlKey)
    {
        return $this->_getResource()->checkUrlKey($urlKey);
    }
}
