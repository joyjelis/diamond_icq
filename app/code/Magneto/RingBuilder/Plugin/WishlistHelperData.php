<?php

namespace Magneto\RingBuilder\Plugin;

class WishlistHelperData
{
    public function __construct(
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productloader
    ) {
        $this->attributeSet = $attributeSet;
        $this->storeManager = $storeManager;
        $this->productloader = $productloader;
    }

    public function afterGetProductUrl(\Magento\Wishlist\Helper\Data $helper, $result, $item, $additional = [])
    {
        if ($item instanceof \Magento\Catalog\Model\Product) {
            $product = $item;
        } else {
            $product = $item->getProduct();
        }
        $store = $this->storeManager->getStore();
        $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
        if ($attributeSetRepository->getAttributeSetName() == "Gemfind Diamonds") {
            $result = $store->getUrl('ringbuilder/diamond/view', ['path' => $product->getUrlKey(), '_secure' => true]);
            $_product = $this->productloader->create()->load($product->getId());
            if ($_product->getGemfindDiamondType()) {
                $result .= '/type/'.$product->getGemfindDiamondType();
            }
        } elseif ($attributeSetRepository->getAttributeSetName() == "Gemfind Ringbuilder") {
            $result = $store->getUrl('ringbuilder/settings/view', ['path' => $product->getUrlKey(), '_secure' => true]);
        }
        return $result;
    }
}
