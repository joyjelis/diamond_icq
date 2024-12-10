<?php
namespace Travash\Education\Model\Config\Source;

class Pagelayout implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return
            [
                
                [
                    'value' => '2columns-left',
                    'label' => __('2columns Left Side Bar')
                ]
            ];
    }
}
