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
namespace FME\ProductSorting\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    const XML_PATH_FME = 'productSorting/'; // section name

    protected $_objectManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->_objectManager = $objectmanager;
        parent::__construct($context);
    }


    public function getConfigValue($field, $storeCode = null)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE);
    }

    public function getGeneralConfig($fieldid, $storeCode = null)
    {
        return $this->getConfigValue(self::XML_PATH_FME . 'general/' . $fieldid, $storeCode);
    }
    public function getConfig($fieldid, $storeCode = null)
    {
        return $this->getConfigValue(self::XML_PATH_FME . $fieldid, $storeCode);
    }

    public function getDirection()
    {

        $field = $this->getConfig('sorting_default/category_sort');
        $dir = $this->getConfig($field . '/direction');

        if ($dir == 2) {
            return "desc";
        } else {
            return "asc";
        }
    }
    public function getDir()
    {

        $field = $this->getConfig('sorting_default/search_sort');
        $dir = $this->getConfig($field . '/direction');

        if ($dir == 2) {
            return "desc";
        } else {
            return "asc";
        }
    }

    public function getCustomerId()
    {

        $customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
        $wishlist = $this->_objectManager->create('Magento\Wishlist\Model\Wishlist');
        $customerId=$customerSession->getCustomerId();
        if ($customerSession->isLoggedIn()) {
            $customerId=$customerSession->getCustomerId();
            $wishlist = $wishlist->loadByCustomerId($customerId)->getItemCollection();
            $idNew = array();
            $wishlistIds = $wishlist->getColumnValues('product_id');
            foreach ($wishlistIds as $id) {
                $idNew[]=(string)$id;
            }

            if (!empty($idNew)) {
                return $idNew;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function getWishlistProduct($ids)
    {
        $dataDB = $ids;
        $action = $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Action');
        foreach ($dataDB as $item) {
            $action->updateAttributes(
                array($item),
                array('wished' => 6),
                0
            );
        }
    }
}
