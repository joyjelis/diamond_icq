<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME Calalog
 * @author    FME extensions <support@fmeextensions.com
>
 * @package   FME_ProductSorting
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/
)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ProductSorting\Block;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Action\Action;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as WishlistCollectionFactory;
use Zend_Db_Expr;

class WidgetProductCollection extends Template implements BlockInterface
{
    protected $context;
    private $configurableProductType;


    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    // protected $abstractProduct;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;
    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $productList;
    /**
     * @var Visibility
     */
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    protected $_bestSellersCollectionFactory;
  
    /**
     * @var string
     */
    protected $_template = "ListProduct.phtml";

    protected $helpers;
    protected $request;
    protected $productVisibility;
    protected $productStatus;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FME\ProductSorting\Helper\Data $helper,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $CollectionFactory,
        // \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        array $data = array()
    ) {
        $this->productCollectionFactory = $CollectionFactory;
        // $this->productList = $listProduct;
        $this->configurableProductType = $configurableProductType;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->helpers = $helper;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->reviewFactory = $reviewFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param $_item
     * @return array
     */
    public function getAddToCartPostParams($_item)
    {
       return \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Catalog\Block\Product\ListProduct')->getAddToCartPostParams($_item);
        // return $this->productList->getAddToCartPostParams($_item);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product';
    }

    /**
     * @param $_item
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRatingSummary($_item)
    {
        $this->reviewFactory->create()->getEntitySummary($_item, $this->storeManager->getStore()->getId());
        $ratingSummary = $_item->getRatingSummary()->getRatingSummary();

        return $ratingSummary;
    }

    /**
     * @param $_item
     * @return mixed
     */
    public function getReviewsCount($_item)
    {
        $_reviewCount = $_item->getRatingSummary()->getReviewsCount();
        return $_reviewCount;
    }
    public function getControllerName()
    {
        $controller = $this->request->getControllerName();
        return $controller;
    }
    public function checkParent($productId)
    {
        $product = $this->configurableProductType->getParentIdsByChild($productId);
        if ($product) {
            return $product[0];
        } else {
            return $productId;
        }
    }
       /**
        * @return string
        */
    public function getTitle()
    {
        $option = $this->helpers->getConfig('widget/option');
        $title = null;
        if ($option) {
            if ($option == 'best_seller') {
                $title = "Our Bestsellers";
            } elseif ($option == 'most_viewed') {
                $title = "Most Viewed Products";
            } elseif ($option == 'top_rated') {
                $title = "Top Rated Products";
            } else {
                $title = "New Arrival Products";
            }
        }
        
        return __($title);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param null $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = array()
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

        $renderPrice = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($renderPrice) {
            $price = $renderPrice->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }

        return $price;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection()
    {
        $option = $this->helpers->getConfig('widget/option');

// \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info("Taimoor");

// \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($option);



        $widgetEnable = $this->helpers->getConfig('widget/enable');
        $page = $this->helpers->getConfig('widget/applyPage');
        $enable = $this->helpers->getConfig('general/enabled');
        $controller = $this->request->getControllerName();
        $col = $this->productCollectionFactory->create();
  
        if ($option && $enable && $widgetEnable) {
            if ($option == 'best_seller') {
             // for bestseller start....................
                $productIds = array();
                $bestSellers = $this->_bestSellersCollectionFactory->create()->setPeriod('month');
                foreach ($bestSellers as $product) {
                    $productIds[] = $product->getProductId();
                }

                $collection = $col->addIdFilter($productIds);
                $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('*')
                ->addStoreFilter($this->getStoreId())->setPageSize($this->getProductsCount());
   
            //bestSeller end........................
            } elseif ($option == 'most_viewed') {
                //for mostviewed.......................................
                $currentStoreId = $this->storeManager->getStore()->getId();
                $collection = $col->addAttributeToSelect(
                    '*'
                )->addViewsCount()->setStoreId(
                    $currentStoreId
                )->addStoreFilter(
                    $currentStoreId
                );
                //for mostviewed end..................................
            } elseif ($option == 'top_rated') {
                //Biggest top_rated start..........................

                $collection=$col->setOrder('top_rated', 'desc');
                $collection->addAttributeToSelect('*')
                ->addStoreFilter($this->getStoreId())->setPageSize($this->getProductsCount());

        //Biggest top_rated end..........................
            } else {
                //new product..................................
                 $collection=$col->setOrder('created_at', 'desc');
                 $collection->addAttributeToSelect('*')
                ->addStoreFilter($this->getStoreId())->setPageSize($this->getProductsCount());
                //new product end ...........................
            }

            $collection->addAttributeToFilter('status', array('in' => $this->productStatus->getVisibleStatusIds()));
            $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
            $items = $collection->getItems();
  
            return $items;
        }
    }

    /**
     * @return string
     */
    public function getCartParamNameURLEncoded()
    {
        return Action::PARAM_NAME_URL_ENCODED;
    }
}
