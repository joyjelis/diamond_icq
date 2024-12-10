<?php

namespace Magneto\RingBuilder\ViewModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\CartFactory as CustomerCart;

class PriceHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    const RINGBUILDER_CURRENCY = "USD";

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        Product $product,
        CustomerCart $cart,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->product = $product;
        $this->cart = $cart;
        $this->currencyFactory = $currencyFactory;
        $this->storeManager = $storeManager;
        $this->priceHelper = $priceHelper;
        $this->priceCurrency = $priceCurrency;
    }

    public function getCurrencyWithFormat($price)
    {
        return $this->priceCurrency->format($price, true, 0);
    }

    public function getRoundedPrice($price)
    {
        return $this->priceCurrency->round($price);
    }

    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * Rounded to nearest 100 dollar
     *
     * @param $price
     * @return float|int
     */
    public function getRoundedBaseCurrencyPrice($price)
    {
        return round($price * 0.01, 0) * 100;
    }

    public function convertToBaseCurrency($price, $formattedprice = 1)
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        if ($currentCurrency != self::RINGBUILDER_CURRENCY) {
            $rate = $this->currencyFactory->create()->load(self::RINGBUILDER_CURRENCY)->getAnyRate($currentCurrency);
            $price = is_int($price) ? $price : (int) $price;
            $returnValue = $price * $rate;
            $returnValue = $this->getRoundedBaseCurrencyPrice($returnValue);
            if ($formattedprice == 1) {
                return $this->getCurrencyWithFormat($returnValue);
            }
            return $this->getRoundedPrice($returnValue);
        }
        $price = round($price * 0.1, 0) * 10;
        if ($formattedprice == 1) {
            return $this->getCurrencyWithFormat($price);
        }
        return $this->getRoundedPrice($price);
    }

    public function convertToUSDCurrency($price, $formattedprice = 1)
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        if ($currentCurrency != self::RINGBUILDER_CURRENCY) {
            $rate = $this->currencyFactory->create()->load($currentCurrency)->getAnyRate(self::RINGBUILDER_CURRENCY);
            $price = round(filter_var($price, FILTER_SANITIZE_NUMBER_INT), 0);
            $price = is_int($price) ? $price : (int) $price;
            $returnValue = $price * $rate;
            if ($formattedprice == 1) {
                return $this->getCurrencyWithFormat($returnValue);
            } else {
                return round($this->getRoundedPrice($returnValue), 0);
            }
        }

        $price = round(filter_var($price, FILTER_SANITIZE_NUMBER_INT), 0);

        if ($formattedprice == 1) {
            return $this->getCurrencyWithFormat($price);
        } else {
            return $this->getRoundedPrice($price);
        }
    }

    public function convertToBaseCurrencyWithUpdate($price, $formattedprice = 1)
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();

        $rate = $this->currencyFactory->create()->load(self::RINGBUILDER_CURRENCY)->getAnyRate($baseCurrency);
        $returnValue = $price * $rate;
        $returnValue = $this->getRoundedBaseCurrencyPrice($returnValue);

        if ($formattedprice == 1) {
            return $this->getCurrencyWithFormat($returnValue);
        } else {
            return $this->getRoundedPrice($returnValue);
        }
    }

    public function getGalleryImages($mainImage, $extraimages)
    {
        $productimage = [];
        $images[] = $mainImage;
        foreach ($extraimages as $extraimg) {
            $images[] = $extraimg;
        }

        if ($images) {
            if (count($images) > 0) {
                foreach ($images as $key => $img) {
                    $imgurl = $img;
                    $productimage[] = [
                        'thumb' => $imgurl,
                        'img' => $imgurl,
                        'full' => $imgurl,
                    ];
                }
            }
        }

        return json_encode($productimage);
    }

    public function addProductsByIds($Ids)
    {
        try {
            $cart = $this->cart->create();
            $cart->addProductsByIds($Ids);
            $cart->getQuote()->setTotalsCollectedFlag(false);
            $cart->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
