<?php

namespace DiamondIcq\Wishlist\Plugin;

class WishlistCollectionPlugin
{
    public function afterGetWishlistItems(\Magento\Wishlist\Block\Customer\Wishlist $subject, $collection)
    {
        $subject->getChildBlock('wishlist_item_pager')
            ->setAvailableLimit(
                [12 => 12, 24 => 24, 36 => 36]
            );
        $limit = $subject->getRequest()->getParam("limit", 12);
        $collection->setPageSize($limit);
        return $collection;
    }
}
