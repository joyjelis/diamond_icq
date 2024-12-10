<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME Calalog
 * @author    FME extensions <support@fmeextensions.com
>
 * @package   FME_ProductSorting
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/
)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ProductSorting\Model\ResourceModel;

use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Stdlib\DateTime;
use \Magento\Framework\Model\ResourceModel\Db\Context;

class Filters extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('fme_productpartsfinder', 'productpartsfinder_id');
    }
    public function checkAttribute($attributecode)
    {
        $select = $this->getConnection()
        ->select()
        ->from($this->getTable('eav_attribute'), array('attribute_code'))->where('attribute_code = ?', $attributecode);
        $code=$this->getConnection()->fetchAll($select);
        if (!empty($code)) {
            return $code['0']['attribute_code'];
        }

        return false;
    }
    public function getBackendtype($attributecode)
    {
        $select = $this->getConnection()
        ->select()
        ->from($this->getTable('eav_attribute'), array('backend_type'))->where('attribute_code = ?', $attributecode);
        $code=$this->getConnection()->fetchAll($select);
        if (!empty($code)) {
            return $code['0']['backend_type'];
        }

        return false;
    }

    public function getReviewCounts($productids)
    {
        $select = $this->getConnection()
        ->select()
        ->from($this->getTable('review_entity_summary'))
        ->where('store_id = 0')
        ->where('entity_pk_value in (?)', $productids)
        ->order('reviews_count DESC ');
        $code=$this->getConnection()->fetchAll($select);
        $productids = array();
        foreach ($code as $key => $value) {
            $productids[] = $value['entity_pk_value'];
        }

        return $productids;
    }
}
