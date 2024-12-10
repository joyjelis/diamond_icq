<?php

namespace Customer\Sell\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Status extends AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Customer\Sell\Model\SellFactory $sell,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->_sell = $sell;
        $this->priceHelper = $priceHelper;
        parent::__construct($context);
    }

    /**
     * Get Config values
     *
     * @param string $config
     *
     * return string
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getOptions()
    {
        return $this->_sell->create()->getStatusOptions();
    }

    public function formatSellPrice($price)
    {
        if (strpos($price, 'to') !== false) {
            $price = explode('to', $price);
            return $this->priceHelper->currency($price[0], true, false) . ' to ' . $this->priceHelper->currency($price[1], true, false);
        }
        return $this->priceHelper->currency($price, true, false);
    }
}
