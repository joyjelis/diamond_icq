<?php

namespace Magneto\AjaxProductsLoad\Block;

use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Wishlist\Helper\Data as wishlistHelper;
use Magneto\AjaxProductsLoad\Helper\Data;

class ListProduct extends Template
{
    /**
     * Block Template
     *
     * @var string
     */
    protected $_template = "Magneto_AjaxProductsLoad::list.phtml";

    /**
     * CategoryID
     *
     * @var int
     */
    protected $categoryId;

    /**
     * __Construct
     *
     * @param Context $context
     * @param Data    $helper
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        wishlistHelper $wishlistHelper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_wishlistHelper = $wishlistHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get wishlist helper.
     *
     * @return \Magento\Wishlist\Helper\Data
     */
    public function getWishlistHelper()
    {
        return $this->_wishlistHelper;
    }

    /**
     * Retrieve add to wishlist params
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToWishlistParams($product)
    {
        return $this->_wishlistHelper->getAddParams($product);
    }

    /**
     * get product Image
     *
     * @param  \Magento\Product\Model\Product $product
     * @return string
     */
    public function getProductImage($product)
    {
        return $this->helper->getProductImage($product);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
        ? $arguments['price_id']
        : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
        ? $arguments['include_container']
        : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
        ? $arguments['display_minimal_price']
        : true;

        /**
         * @var \Magento\Framework\Pricing\Render $priceRender 
        */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $price = $priceRender->render(
            FinalPrice::PRICE_CODE,
            $product,
            $arguments
        );

        return $price;
    }
}
