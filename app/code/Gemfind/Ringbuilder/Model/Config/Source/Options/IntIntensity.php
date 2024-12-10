<?php

namespace Gemfind\Ringbuilder\Model\Config\Source\Options;

class IntIntensity extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        return [
            [
                'label' =>'Faint',
                'value' =>'faint'
            ],
            [
                'label' =>'V.Light',
                'value' =>'very light'
            ],
            [
                'label' =>'Light',
                'value' =>'light'
            ],
            [
                'label' =>'F.Light',
                'value' =>'fancy light'
            ],
            [
                'label' =>'Fancy',
                'value' =>'fancy'
            ],
            [
                'label' =>'Dark',
                'value' =>'fancy dark'
            ],
            [
                'label' =>'Intense',
                'value' =>'fancy intense'
            ],
            [
                'label' =>'Vivid',
                'value' =>'fancy vivid'
            ],
            [
                'label' =>'Deep',
                'value' =>'fancy deep'
            ]
        ];
    }
}
