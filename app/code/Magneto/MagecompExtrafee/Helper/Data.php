<?php

namespace Magneto\MagecompExtrafee\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends AbstractHelper
{
    protected $priceCurrency;
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->_priceCurrency = $priceCurrency;
    }
    public function convertPrice($amount = 0, $store = null, $currency = null)
    {
        $logger = \Magento\Framework\App\ObjectManager::getInstance()->get('\Psr\Log\LoggerInterface');

        $logger->debug("I'm coming from HELPER :: Amount : ".$amount." ---- store : ".$store." ---- currency : ".$currency); // add logs in debug.log


        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceCurrencyObject = $objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface');
        //instance of PriceCurrencyInterface
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        //instance of StoreManagerInterface
        if ($store == null) {
            $store = $storeManager->getStore()->getStoreId();
            //get current store id if store id not get passed
        }
        $rate = $priceCurrencyObject->convert($amount, $store, $currency);
        //it return price according to current store from base currency
        $logger->debug("New Currency Rate : ".$rate); // add logs in debug.log
     
        //If you want it in base currency then use:
        //$rate = $this->_priceCurrency->convert($amount, $store) / $amount;

        $logger->debug("New Currency Rate : ".$rate); // add logs in debug.log

       // $amount = $amount / $rate;

        $logger->debug("New Currency Amount : ".$amount); // add logs in debug.log

         
        return $rate;
    }

    public function getIsProductsEngraved(\Magento\Quote\Model\Quote $quote)
    {
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
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
}
