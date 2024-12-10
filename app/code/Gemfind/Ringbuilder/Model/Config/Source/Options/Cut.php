<?php

namespace Gemfind\Ringbuilder\Model\Config\Source\Options;

class Cut extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        return [
            [
                'label' =>'Ideal',
                'value' =>1
            ],
            [
                'label' =>'Excellent',
                'value' =>2
            ],
            [
                'label' =>'V.Good',
                'value' =>3
            ],
            [
                'label' =>'Good',
                'value' =>4
            ],
            [
                'label' =>'Fair',
                'value' =>5
            ]
        ];
    }
}
