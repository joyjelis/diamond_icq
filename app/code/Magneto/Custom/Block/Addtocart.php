<?php
namespace Magneto\Custom\Block;

use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;

class Addtocart extends \Magento\Framework\View\Element\Template
{
    private $getSalableQuantityDataBySku;
    protected $customer;
    private $wishlist;
    private static $wishlistItems;
    protected $_registry;
    protected $_productRepository;
    protected $_wishlistHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customer,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->customer = $customer;
        $this->wishlist = $wishlist;
        $this->_registry = $registry;
        $this->_productRepository = $productRepository;
        $this->_wishlistHelper = $wishlistHelper;
        $this->stockRegistry = $stockRegistry;
        parent::__construct($context, $data);
    }

    public function getSalableQuantity($sku)
    {
        $salable = $this->getSalableQuantityDataBySku->execute($sku);
        return json_encode($salable);
    }
    public function getCustomerSession()
    {
        $customer = $this->customer;
        return $customer;
    }
    public function getWishlist()
    {
        return $this->wishlist;
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function getSku($pid)
    {
        $product = $this->_productRepository->getById($pid);
        return $product->getSku();
    }

    public function getDiscount($pid = null, $_finalPrice = null, $_price = null)
    {
        if ($pid) {
            $product = $this->_productRepository->getById($pid);
            $_finalPrice =$product->getFinalPrice();
            $_price = $product->getPrice();

            $_savingPercent = 0;
            if ($_finalPrice < $_price) {
                  $_savingPercent = 100 - round(($_finalPrice / $_price)*100);
            }
            return $_savingPercent;
        } else {
            $_savingPercent = 0;
            if ($_finalPrice < $_price) {
                  $_savingPercent = 100 - round(($_finalPrice / $_price)*100);
            }
            return $_savingPercent;
        }
    }

    public function getProduct($pid)
    {
        $product = $this->_productRepository->getById($pid);
        return $product;
    }

    public function getSettingPrice($pid)
    {
        return $this->getProduct($pid)->getData('setting_price');
    }

    public function getWishlistItems()
    {
        if (!is_array(self::$wishlistItems)) {
            self::$wishlistItems = [];
            $collection = $this->_wishlistHelper->getWishlistItemCollection();
            foreach ($collection as $_wishlist_item) {
                self::$wishlistItems[$_wishlist_item->getProduct()->getId()] = $_wishlist_item;
            }
        }
        return self::$wishlistItems;
    }

    public function getStockItem($productId)
    {
        return $this->stockRegistry->getStockItem($productId);
    }
}
