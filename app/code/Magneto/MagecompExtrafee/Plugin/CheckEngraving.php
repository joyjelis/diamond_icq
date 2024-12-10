<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_Quote
 */

namespace Magneto\MagecompExtrafee\Plugin;

use Magecomp\Extrafee\Helper\Data as ExtrafeeHelper;

class CheckEngraving
{

    public function aroundIsModuleEnabled(ExtrafeeHelper $subject, $proceed)
    {
        
        $isEnabled = $proceed();
        if ($this->getIsProductsEngraved()) {
            return $isEnabled;
        } else {
            return false;
        }

        //return $proceed($configPath, $sendTo, $data);
    }

    public function getIsProductsEngraved()
    {
        $logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->get("Psr\Log\LoggerInterface");
        $logger->debug("comes to override function -helper"); // add logs in debug.log
       
        $cartsmodel = $objectManager->get('Magento\Checkout\Model\Cart');
        $quote = $cartsmodel->getQuote();
        $cartAllItems = $quote->getAllItems();
        
        $engraveCount = 0;
        foreach ($cartAllItems as $item) {
            foreach ($item->getOptions() as $option) {
                $optionData = $option->toArray();
                if ($option['code']=='additional_options') {
                   // print_r($option['value']);
                    $additionalOptionsData = json_decode($option['value'], true);
                    if (!empty($additionalOptionsData)) {
                        foreach ($additionalOptionsData as $aodata) {
                            if (isset($aodata['label']) && trim($aodata['label'])!='' && trim($aodata['label'])=='Engraving Text') {
                                $engraveCount++;
                            }
                        }
                    }
                }
            }
        }
        $logger->debug("engraveCount : ".$engraveCount); // add logs in debug.log
        if ($engraveCount>0) {
            return true;
        } else {
            return false;
        }
    }
}
