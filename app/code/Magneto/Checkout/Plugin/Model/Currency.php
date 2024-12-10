<?php
namespace Magneto\Checkout\Plugin\Model;

class Currency
{
    public function aroundConvert($subject, $proceed, $price, $toCurrency = null)
    {
        $price = $proceed($price, $toCurrency);
        // you logic
        // warning ... logic affects the price of shipping
        return round($price * 0.1, 0) * 10; // round off to the nearest 10th dollar
    }
}
