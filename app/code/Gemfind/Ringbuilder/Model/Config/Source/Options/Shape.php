<?php

namespace Gemfind\Ringbuilder\Model\Config\Source\Options;

class Shape extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        return [
            [
                'label' =>'Round',
                'value' =>'round'
            ],
            [
                'label' =>'Princess',
                'value' =>'princess'
            ],
            [
                'label' =>'Emerald',
                'value' =>'emerald'
            ],
            [
                'label' =>'Radiant',
                'value' =>'radiant'
            ],
            [
                'label' =>'Cushion',
                'value' =>'cushion'
            ],
            [
                'label' =>'Pear',
                'value' =>'pear'
            ],
            [
                'label' =>'Marquise',
                'value' =>'marquise'
            ],
            [
                'label' =>'Oval',
                'value' =>'oval'
            ],
            [
                'label' =>'Asscher',
                'value' =>'asscher'
            ],
            [
                'label' =>'Heart',
                'value' =>'heart'
            ]
        ];
    }
}
