<?php

namespace Magneto\RingBuilder\Plugin;

use Magento\Quote\Model\Quote;

class RestictSettingDiamond
{
    public function __construct(
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_request = $request;
        $this->attributeSet = $attributeSet;
        $this->_productloader = $_productloader;
    }

    public function beforeRemoveItem(Quote $subject, $itemId)
    {
        $diamondtype = $subject->getItemById($itemId);
        $attributeSetRepository = $this->attributeSet->get($diamondtype->getProduct()->getAttributeSetId());
        if ($attributeSetRepository->getAttributeSetName() == "Gemfind Diamonds") {
            $diamondsku = $diamondtype->getSku();
            foreach ($subject->getAllItems() as $item) {
                $attributeSetRepository = $this->attributeSet->get($item->getProduct()->getAttributeSetId());
                if ($attributeSetRepository->getAttributeSetName() == "Ringbuilder") {
                    $options = $item->getBuyRequest()->getOptions();
                    if ($options && is_array($options)) {
                        foreach ($options as $key => $value) {
                            if ('dl-' . $value == $diamondsku) {
                                throw new \Magento\Framework\Exception\LocalizedException(
                                    __('We can\'t remove the item.')
                                );
                                return $this;
                            }
                        }
                    }
                }
            }
        }

        return [$itemId];
    }
}
