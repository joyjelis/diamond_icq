<?php
 
namespace Magneto\MyAccountOrderCustomization\ViewModel;
 
use Magento\Framework\View\Element\Block\ArgumentInterface;
 
class OrderHelpers implements ArgumentInterface
{
     public function __construct(
        \Magneto\MyAccountOrderCustomization\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    public function getProductsImageLink($productId)
    {   
        return $this->helper->getProductImage($productId);
    }
    
 
}