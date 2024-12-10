<?php
namespace Magneto\CustomerAccountCustomization\ViewModel;
 
use Magento\Framework\View\Element\Block\ArgumentInterface;
 
class CustomerAccountViewModel implements ArgumentInterface
{
    public function __construct(
        \Magneto\CustomerAccountCustomization\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    public function getCustomerPhoneNo()
    {
        return $this->helper->getCustomerPhone();
    }
}
