<?php

namespace Magneto\CurrencyChangeTab\Model\Source;

class CurrencyOption extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $currencyInterface
    ) {
        $this->_storeManager = $storeManager;
        $this->_currencyInterface = $currencyInterface;
    }

    public function getAllOptions()
    {

        //$this->_options = [['label'=>'Please select', 'value'=>'']];
        $availableCurrencies = $this->_storeManager->getStore()->getAvailableCurrencyCodes();
        foreach ($availableCurrencies as $currencyCode) {
            $currencyNames = $this->_currencyInterface->getCurrency($currencyCode)->getName();
            $currencySymbol = $this->_currencyInterface->getCurrency($currencyCode)->getSymbol();
            $this->_options[] = ['label'=> $currencyNames, 'value' => $currencyCode];
        }
        return $this->_options;
    }
    
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
