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
class Status implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'pending', 'label' => __('Pending')),
            array('value' => 'processing', 'label' => __('Processing')),
            array('value' => 'fraud', 'label' => __('Suspected Fraud')),
            array('value' => 'complete', 'label' => __('Complete')),
            array('value' => 'closed', 'label' => __('Closed')),
            array('value' => 'canceled', 'label' => __('Canceled')),
            array('value' => 'holded', 'label' => __('On Hold'))

        );
    }
}
