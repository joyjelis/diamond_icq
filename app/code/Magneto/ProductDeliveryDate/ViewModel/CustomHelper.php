<?php
 
namespace Magneto\ProductDeliveryDate\ViewModel;
 
use Magento\Framework\View\Element\Block\ArgumentInterface;
 
class CustomHelper implements ArgumentInterface
{
    public function __construct(
        \Magneto\ProductDeliveryDate\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    public function getMaxDayQuoteProducts()
    {
        return $this->helper->getMaxDayQuoteProducts();
    }
}
