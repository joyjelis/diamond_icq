<?php
 
namespace Magneto\FreeEngraving\ViewModel;
 
use Magento\Framework\View\Element\Block\ArgumentInterface;
 
class RingBuilderEngravingHelper implements ArgumentInterface
{
    
    public function __construct(
        \Magneto\FreeEngraving\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    public function getRingEngravingCookieData($id)
    {   
        return $this->helper->getRingEngravingCookieData($id);
    }
}