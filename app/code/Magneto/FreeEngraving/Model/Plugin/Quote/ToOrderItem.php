<?php
namespace Magneto\FreeEngraving\Model\Plugin\Quote;

use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
use Magento\Framework\Serialize\Serializer\Json;

class ToOrderItem
{

    
    public function __construct(Json $serializer = null)
    {
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function aroundConvert(
        QuoteToOrderItem $subject,
        \Closure $proceed,
        $item,
        $data = []
    ) {
        // Get Order Item
        $orderItem = $proceed($item, $data);
        $additionalOptions = $item->getOptionByCode('additional_options');
        // Check if there is any additional options in Quote Item
        if (!empty($additionalOptions)) {
            $options = $orderItem->getProductOptions();
            $options['additional_options'] = $this->serializer->unserialize($additionalOptions->getValue());
            $orderItem->setProductOptions($options);
        }

        return $orderItem;
    }
}
