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
namespace FME\ProductSorting\Model\System;

use Magento\Framework\Option\ArrayInterface;

class SortOptions implements ArrayInterface
{
    const BEST_SELLER  = "best_seller";
    const TOP_RATED = "top_rated";
    const NEW_ARRIVALS = "created_at";
    const MOST_VIEWED = "most_viewed";
    const REVIEW_COUNT = "review_count";
    const SAVING = "saving";
    const PRICE_ASC = "price_asc";
    const PRICE_DESC = "price_desc";
    const STOCK_QTY = "stockquantity";
    const WISH_LIST = "wished";

    /**
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }

        return $options;
    }

    /**
     * Return options
     *
     * @return array
     */
    public function getOptionHash()
    {
        return array(
            self::BEST_SELLER  => __('Best Seller'),
            self::TOP_RATED => __('Top Rated'),
            self::NEW_ARRIVALS => __('New Arrivals'),
            self::MOST_VIEWED => __('Most Viewed'),
            self::REVIEW_COUNT => __('Review Count'),
            self::SAVING  => __('Biggest Saving'),
            self::PRICE_ASC => __('Price: low to high'),
            self::PRICE_DESC => __('Price: high to low'),
            self::STOCK_QTY => __('Stock Quantity'),
            self::WISH_LIST => __('Now in Wishlists'),
        );
    }
}
