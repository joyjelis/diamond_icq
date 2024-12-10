<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magneto\ProductDeliveryDate\Block\Product;

use \Magneto\ProductDeliveryDate\Helper\Data;

/**
 * Product View block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Catalog\Block\Product\View
{
    
    public function getProductEstimateDeliveryDate()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('Magneto\ProductDeliveryDate\Helper\Data');
        $expectedDeliveryDate = '';
        if ($helper->isEnabled()) {
            if (null !== $this->getProduct()->getCustomAttribute('product_delivery_days')) {
                $productDeliveryDays = $this->getProduct()->getCustomAttribute('product_delivery_days')->getValue();
                if (trim($productDeliveryDays)!='' && trim($productDeliveryDays)!=0) {
                    $currentDate =  date('Y-m-d');
                    $expectedDeliveryDate = date("Y-m-d", strtotime($currentDate." + $productDeliveryDays day"));
                    $expectedDeliveryDate = $helper->showDeliveryDateFormat($expectedDeliveryDate);
                }
            }
        }
        
        return $expectedDeliveryDate;
    }
}
