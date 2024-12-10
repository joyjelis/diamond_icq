<?php

namespace Gemfind\Ringbuilder\Model\Config\Source\Options;

class Resultperpage extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        return [
            [
                'label' =>'Records Per Page: 12',
                'value' =>12
            ],
            [
                'label' =>'Records Per Page: 24',
                'value' =>24
            ],
            [
                'label' =>'Records Per Page: 48',
                'value' =>48
            ],
            [
                'label' =>'Records Per Page: 99',
                'value' =>99
            ]
        ];
    }
}
