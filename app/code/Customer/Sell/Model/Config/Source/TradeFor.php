<?php declare(strict_types=1);

namespace Customer\Sell\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TradeFor implements OptionSourceInterface
{
    const TRADE_FOR_CASH = 1;
    const TRADE_FOR_BIGGER_DIAMOND = 2;
    const REQUEST_QUOTE = 3;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::TRADE_FOR_CASH,
                'label' => __('Trade for Cash')
            ],
            [
                'value' => self::TRADE_FOR_BIGGER_DIAMOND,
                'label' => __('Trade for Bigger Diamond')
            ],
            [
                'value' => self::REQUEST_QUOTE,
                'label' => __('Request Quote')
            ]
        ];
    }
}
