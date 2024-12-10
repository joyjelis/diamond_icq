<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_Quote
 */

namespace Magneto\MagecompExtrafee\Plugin;

use Magecomp\Extrafee\Helper\Data as ExtrafeeHelper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magneto\MagecompExtrafee\Helper\Data as MyHelper;

class ConvertFeeAmount
{

    const CONFIG_CUSTOM_IS_ENABLED = 'Extrafee/Extrafee/status';
    const CONFIG_CUSTOM_FEE = 'Extrafee/Extrafee/Extrafee_amount';
    const CONFIG_FEE_LABEL = 'Extrafee/Extrafee/name';
    const CONFIG_MINIMUM_ORDER_AMOUNT = 'Extrafee/Extrafee/minimum_order_amount';

    protected $priceCurrency;
    protected $scopeConfig;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        ScopeConfigInterface $scopeConfig,
        MyHelper $myHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->myHelper = $myHelper;
    }


    public function aroundGetExtrafee(ExtrafeeHelper $subject, $proceed)
    {
        
        $isEnabled = $proceed();
        $isEnabled = $this->convertExtraFeeCurrency();
          return $isEnabled;
        
        //return $proceed($configPath, $sendTo, $data);
    }

    public function convertExtraFeeCurrency()
    {
        
        $logger = \Magento\Framework\App\ObjectManager::getInstance()->get('\Psr\Log\LoggerInterface');
        $logger->debug("override for converting extra fee currency"); // add logs in debug.log

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $currencycode = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $newConvertedPrice  = $this->myHelper->convertPrice($this->scopeConfig->getValue(self::CONFIG_CUSTOM_FEE, $storeScope), null, $currencycode);
        $logger->debug("Our new converted price : ".$newConvertedPrice); // add logs in debug.log
        return $newConvertedPrice;
    }

    /*
    public function convertPrice($amount = 0, $store = null, $currency = null)
    {
        $logger = \Magento\Framework\App\ObjectManager::getInstance()->get('\Psr\Log\LoggerInterface');

        $logger->debug("Amount : ".$amount." ---- store : ".$store." ---- currency : ".$currency); // add logs in debug.log


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
        $rate = $this->_priceCurrency->convert($amount, $store) / $amount;

        $logger->debug("New Currency Rate : ".$rate); // add logs in debug.log

        $amount = $amount / $rate;

        $logger->debug("New Currency Amount : ".$amount); // add logs in debug.log


        return $amount;

    }

    */
}
