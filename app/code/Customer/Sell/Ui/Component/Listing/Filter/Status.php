<?php

namespace Customer\Sell\Ui\Component\Listing\Filter;

use Customer\Sell\Helper\Status as SellStatus;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * @var \Customer\Sell\Helper\Status
     */
    protected $statusHelper;

    /**
     * @param SellStatus $statusHelper
     */
    public function __construct(SellStatus $statusHelper)
    {
        $this->statusHelper = $statusHelper;
    }

    /**
     * Return Options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        $options = $this->statusHelper->getOptions();
        foreach ($options as $value => $label) {
            $result[] = [
                'label' => $label,
                'value' => $value,
            ];
        }
        return $result;
    }

    public function getOptionsArray()
    {
        $result = [];
        $options = $this->statusHelper->getOptions();
        foreach ($options as $value => $label) {
            $result[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $result;
    }
}
