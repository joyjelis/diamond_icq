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
namespace FME\ProductSorting\Plugin;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use FME\ProductSorting\Helper\Data;

class ResultOption
{
    protected $catalogLayer;
    protected $_dataHelper;
    public function __construct(
        LayerResolver $layerResolver,
        Data $dataHelper
    ) {
        
        $this->catalogLayer = $layerResolver->get();
        $this->_dataHelper = $dataHelper;
    }
    /**
     * Retrieve search list toolbar block
     *
     * @return ListProduct
     */
    public function getListBlock()
    {
        return $this->getChildBlock('search_result_list');
    }

    public function aroundSetListOrders(\Magento\CatalogSearch\Block\Result $subject, callable $proceed)
    {
        $category = $this->catalogLayer->getCurrentCategory();
        /* @var $category \Magento\Catalog\Model\Category */
        $availableOrders = $category->getAvailableSortByOptions();
        $availableOrders['relevance'] = __('Relevance');
        $result= $availableOrders;
        return $result;
    }
}
