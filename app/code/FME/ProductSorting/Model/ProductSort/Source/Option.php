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
namespace FME\ProductSorting\Model\ProductSort\Source;

/**
 * Class Profile
 * @package FME\ProductSorting\Model\ProductSort\Source
 */
class Option implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'relevance', 'label' => __('Relevance')),
            array('value' => 'name', 'label' => __('Product Name')),
            array('value' => 'best_seller', 'label' => __('Best Sellers')),
            array('value' => 'most_viewed', 'label' => __('Most Viewed')),
            array('value' => 'top_rated', 'label' => __('Top Rated')),
            array('value' => 'created_at', 'label' => __('New Arrival')),
            array('value' => 'price_asc', 'label' => __('Price: low to high')),
            array('value' => 'stockquantity', 'label' => __('Stock Quantity')),
            array('value' => 'wished', 'label' => __('Now in Wishlist')),
            array('value' => 'saving', 'label' => __('Biggest Saving')),
            array('value' => 'price', 'label' => __('Price')),
            array('value' => 'price_desc', 'label' => __('Price: high to low'))

        );
    }
}
