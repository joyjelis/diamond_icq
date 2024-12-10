<?php
/**
 * @author    Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package   Magneto_RepoVersion
 */

namespace Magneto\RepoVersion\Block;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class Currency extends Template
{

    protected $prices = [];

    protected $labels = [];

    protected $commandparams;

    protected $type;

    protected $url;

    /**
     * Block Template
     *
     * @var string
     */
    protected $_template = "Magneto_RepoVersion::menu.phtml";

    /**
     *  _Construct
     *
     * @param Context $context
     * @param Data    $helper
     * @param array   $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->currencyFactory = $currencyFactory;
        $this->_storeManager = $storeManager;
        $this->priceHelper = $priceHelper;
        $this->priceCurrency = $priceCurrency;
        $this->CollectData();
    }

    public function CollectData()
    {
        if ($this->getData('prices_under') != null) {
            $this->prices = explode(',', $this->getData('prices_under'));
            $this->type = 'prices_under';
        }

        if ($this->getData('labels') != null) {
            $this->labels = explode(',', $this->getData('labels'));
        }

        if ($this->getData('url') != null) {
            $this->url = $this->getData('url');
        }

        if ($this->getData('commandparams') != null) {
            parse_str($this->getData('commandparams'), $this->commandparams);
        }
    }

    public function getMenuItems(): array
    {
        $items = [];
        foreach ($this->prices as $key => $price) {
            $label = $this->getMenuTitle();
            if (isset($this->labels[0])) {
                $label = $this->labels[0];
            }

            if (isset($this->labels[$key])) {
                $label = $this->labels[$key];
            }

            $items[] = $this->makeitem($price, $label);
        }

        return $items;
    }

    public function translatelabel($label)
    {
        $text = str_replace("[currency]", '', $label);
        $translatelabel = __(trim(str_replace("[currency]", '', $label)));
        return str_replace($text, $translatelabel, $label);
    }

    public function makeitem($price, $label): array
    {
        $params = [];
        $convertedprice = $this->ConvertCurrency((int) $price);
        $formattedprice = $this->getCurrencyWithFormat($convertedprice);
        $label = $this->translatelabel($label);
        $label = str_replace("[currency]", ' ' . $formattedprice, $label);

        if ($this->type == "prices_under") {
            $params['price'] = "-" . $price;
        }

        if (is_array($this->commandparams)) {
            foreach ($this->commandparams as $key => $value) {
                $params[$key] = $value;
            }
        }
        $url = $this->getUrl('', ['_direct' =>$this->url]) . '?' . http_build_query($params);

        return [
        'url' => $url,
        'label' => $label,
        ];
    }

    public function getMenuTitle()
    {
        return $this->getData('menutitle');
    }

    public function getClassnames()
    {
        return $this->getData('desktop_class');
    }

    public function getMobileClassnames()
    {
        return $this->getData('mobile_class');
    }

    public function getCurrencyWithFormat($price)
    {
        return $this->priceCurrency->format($price, false, 0);
    }

    public function getRoundedPrice($price)
    {
        return $this->priceCurrency->round($price);
    }

    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    public function ConvertCurrency($price)
    {
        $currentCurrency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $baseCurrency = $this->_storeManager->getStore()->getBaseCurrency()->getCode();
        if ($currentCurrency != $baseCurrency) {
            $price = round(filter_var($price, FILTER_SANITIZE_NUMBER_INT), 0);
            $rate = $this->currencyFactory->create()->load($baseCurrency)->getAnyRate($currentCurrency);
            $returnValue = $price * $rate;
            $price = $returnValue;
        }

        return $price;
    }
}
