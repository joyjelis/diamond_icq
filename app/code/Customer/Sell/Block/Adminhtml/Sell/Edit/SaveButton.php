<?php

declare(strict_types=1);

namespace Customer\Sell\Block\Adminhtml\Sell\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Save The Sell record
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Sell'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
