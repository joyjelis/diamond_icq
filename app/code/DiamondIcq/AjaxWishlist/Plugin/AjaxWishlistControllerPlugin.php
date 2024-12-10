<?php
namespace DiamondIcq\AjaxWishlist\Plugin;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Wishlist\Controller\AbstractIndex;
use Magento\Wishlist\Helper\Data;

/**
 * Class AjaxWishlistControllerPlugin
 *
 * @see \Magento\Wishlist\Controller\Index\Add
 * @see \Magento\Wishlist\Controller\Index\Remove
 * @see \Magento\Wishlist\Controller\Index\Update
 */
class AjaxWishlistControllerPlugin
{
    /**
     * @var Json
     */
    private $resultJson;
    
    /**
     * @var Magento\Wishlist\Helper\Data
     */
    private $wishlistHelper;

    private $wishlistItemProduct;

    /**
     * AjaxWishlistControllerPlugin constructor.
     *
     * @param Json $resultJson
     */
    public function __construct(
        Json $resultJson,
        \Magento\Wishlist\Helper\Data $wishlistHelper
    ) {
        $this->resultJson = $resultJson;
        $this->wishlistHelper = $wishlistHelper;
    }

    /**
     * @param AbstractIndex $subject
     * @param ResultInterface $result
     * @return $this|ResultInterface
     */
    public function beforeExecute(AbstractIndex $subject)
    {
        $itemId = $subject->getRequest()->getParam('item', '');
        if (!empty($itemId)) {
            $wishlistItems = $this->wishlistHelper->getWishlistItemCollection();
            foreach ($wishlistItems as $item) {
                if ($item->getId() == $itemId) {
                    $this->wishlistItemProduct = $item->getProduct();
                }
            }
        }
    }

    /**
     * @param AbstractIndex $subject
     * @param ResultInterface $result
     * @return $this|ResultInterface
     */
    public function afterExecute(AbstractIndex $subject, ResultInterface $result)
    {
        if ($subject->getRequest()->isAjax()) {
            if (!empty($this->wishlistItemProduct)) {
                $data = $this->wishlistHelper->getAddParams($this->wishlistItemProduct);
            } else {
                $productId = $subject->getRequest()->getParam('product', '');
                $wishlistItems = $this->wishlistHelper->getWishlistItemCollection();
                foreach ($wishlistItems as $item) {
                    if ($item->getProduct()->getId() == $productId) {
                        $data = $this->wishlistHelper->getRemoveParams($item);
                        break;
                    }
                }
            }
            return $this->resultJson->setData([
                'errors' => false,
                'messages' => __('Success'),
                'data' => $data,
            ]);
        }
        return $result;
    }
}
