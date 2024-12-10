<?php

namespace Magneto\CartAttributes\Plugin\Checkout\CustomerData;
use Magento\Quote\Model\Quote\Item;

class DefaultItem
{
    public function aroundGetItemData($subject, \Closure $proceed, Item $item) {
        $data = $proceed($item);
		$_product = $item->getProduct();
		$result = [];
        $result['engraving_font'] = __('Engraving Font');
        $result['engraving_text'] = __('Engraving Text');
        return array_merge($data,$result);
    }
}