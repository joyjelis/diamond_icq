<?php

namespace Gemfind\Ringbuilder\Model\Config\Source\Options;

class Diamondresultperpage extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        return [
            [
                'label' =>20,
                'value' =>20
            ],
            [
                'label' =>50,
                'value' =>50
            ],
            [
                'label' =>100,
                'value' =>100
            ]
        ];
    }
}
