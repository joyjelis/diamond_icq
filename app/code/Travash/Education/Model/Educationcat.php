<?php
namespace Travash\Education\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Educationcat
 * @package Travash\Education\Model
 */
class Educationcat extends AbstractModel
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Travash\Education\Model\ResourceModel\Educationcat');
    }

    /**
     * @param $urlKey
     * @return mixed
     */
    public function checkUrlKey($urlKey)
    {
        if (method_exists($this->_getResource(), 'checkUrlKey')) {
            return $this->_getResource()->checkUrlKey($urlKey);
            ;
        }
        return null;
    }
}
