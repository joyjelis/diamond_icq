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
namespace FME\ProductSorting\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\Element;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Url\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Output as OutputHelper;
use Magento\Catalog\Block\Product\AbstractProduct;

/**
 * Product list
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class CustomListProduct extends AbstractProduct implements IdentityInterface
{
/**
 * Default toolbar block name
 *
 * @var string
 */
    protected $_defaultToolbarBlock = Toolbar::class;
/**
 * Product Collection
 *
 * @var AbstractCollection
 */
    protected $_productCollection;
/**
 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
 */
    protected $_prodCollection;
/**
 * Catalog layer
 *
 * @var Layer
 */
    protected $_catalogLayer;
/**
 * @var PostHelper
 */
    protected $_postDataHelper;
/**
 * @var Data
 */
    protected $urlHelper;
/**
 * @var CategoryRepositoryInterface
 */
    protected $categoryRepository;
/**
 * @var \Magento\Framework\App\Request\Http
 */
    protected $request;
/**
 */
    protected $resourceModel;
/**
 * @var Product\Visibility
 */
    protected $productVisibility;
/**
 * @var Product\Attribute\Source\Status
 */
    protected $productStatus;
/**
 * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
 */
    protected $configurable;
/**
 * @var \Magento\GroupedProduct\Model\Product\Type\Grouped
 */
    protected $grouped;
    protected $storeManager;

/**
 */
    protected $helper;
/**
 * @var \Magento\Bundle\Model\Product\Type
 */
    protected $bundle;
/**
 * @var \Magento\Framework\Registry
 */
    protected $registry;
/**
 * @var \Magento\Catalog\Model\CategoryFactory
 */
    protected $_categoryFactory;
    protected $productRepository;
    protected $searchCriteriaBuilder;
    protected $filterBuilder;
    protected $filterGroupBuilder;
    
    
/**
 * @param Context $context
 * @param PostHelper $postDataHelper
 * @param Resolver $layerResolver
 * @param CategoryRepositoryInterface $categoryRepository
 * @param Data $urlHelper
 * @param array $data
 * @param OutputHelper|null $outputHelper
 */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_prodCollection,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Framework\App\Request\Http $request,
        \FME\ProductSorting\Model\ResourceModel\Filters $resourceModel,
        \FME\ProductSorting\Helper\Data $helper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Bundle\Model\Product\Type $bundle,
        \Magento\GroupedProduct\Model\Product\Type\Grouped $grouped,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        array $data = array(),
        ?OutputHelper $outputHelper = null
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->resourceModel = $resourceModel;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $data['outputHelper'] = $outputHelper ?? ObjectManager::getInstance()->get(OutputHelper::class);
        parent::__construct(
            $context,
            $data
        );
        $this->request = $request;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->productStatus = $productStatus;
        $this->registry     = $registry;
        $this->productVisibility = $productVisibility;
        $this->configurable = $configurable;
        $this->_categoryFactory = $categoryFactory;
        $this->grouped = $grouped;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->_prodCollection = $_prodCollection;
        $this->bundle = $bundle;
    }
    protected function getCurrentDirection()
    {
        $param = $this->request->getParams();
        if (isset($param['product_list_dir'])) {
            return 'desc';
        }

        return 'asc';
    }
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->initializeProductCollection();
        }

        if ($this->helper->getGeneralConfig('enabled')) {
            $param = $this->request->getParams();
            $outOfStock = $this->helper->getConfig('general/stock_status');
            $noImage = $this->helper->getConfig('general/stock_qty');
            $productsCollection = $this->_prodCollection->create();
            $collectionByIds = $productsCollection->addAttributeToSelect('*');
            $category = $this->_categoryFactory->create()->load($this->request->getParam('id'));
            $collectionByIds->addCategoryFilter($category);
            $collectionByIds->addStoreFilter($this->storeManager->getStore()->getId());
            foreach ($param as $key => $value) {
                $code = false;
                $cat = false;
                if ($key == 'cat') {
                    $cat  = true;
                } else {
                    $code  = $this->resourceModel->checkAttribute($key);
                }

                if ($key != 'product_list_order' && $key != 'id' && $key != 'p' && $key != 'product_list_limit' && $code) {
                    $value = explode(",", $value);
                    $parentids = array();
                    $backendtype = $this->resourceModel->getBackendtype($key);
                    foreach ($value as $keyy => $valuee) {
                        $finalprodids = array();
                        if ($backendtype == 'decimal') {
                            $valuee = explode("-", $valuee);
                            $collectionByIds->addFieldToFilter('price', array('from' => $valuee['0'], 'to' => $valuee['1']));
                        } else {
                            $collectionByIds->addAttributeToFilter($key, array("finset" => $valuee));
                        }
                    }
                }

                if ($key != 'product_list_order' && $key != 'id' && $key != 'p' && $key != 'product_list_limit' && $cat) {
                    $value = explode(",", $value);
                    $parentids = array();
                    foreach ($value as $keyy => $valuee) {
                        $finalprodids = array();
                        $category = $this->_categoryFactory->create()->load($valuee);
                        $collectionByIds->addCategoryFilter($category);
                    }
                }
            }

            $collectionByIds->addAttributeToFilter('status', array('in' => $this->productStatus->getVisibleStatusIds()));
            $collectionByIds->setVisibility($this->productVisibility->getVisibleInSiteIds());
            foreach ($param as $key => $value) {
                if ($key == 'product_list_order') {
                    if ($value == 'created_at') {
                        $collectionByIds->setOrder('new', $this->getCurrentDirection());
                    } elseif ($value == 'best_seller') {
                        $collectionByIds->setOrder('status_order', $this->getCurrentDirection());
                    } elseif ($value == 'review_count') {
                        $joinConditions = 'e.entity_id = review_entity_summary.entity_pk_value';
                        $collectionByIds->getSelect()->join(
                            array('review_entity_summary'),
                            $joinConditions,
                            array()
                        )
                        ->where("review_entity_summary.store_id=".$this->storeManager->getStore()->getId()."")
                        ->order('review_entity_summary.reviews_count '.$this->getCurrentDirection().' ');
                    } elseif ($value == 'top_rated') {
                        $joinConditions = 'e.entity_id = review_entity_summary.entity_pk_value';
                        $collectionByIds->getSelect()->join(
                            array('review_entity_summary'),
                            $joinConditions,
                            array()
                        )
                        ->where("review_entity_summary.store_id=".$this->storeManager->getStore()->getId()."")
                        ->order('review_entity_summary.rating_summary '.$this->getCurrentDirection().' ');
                    } elseif ($value == 'most_viewed') {
                        $collectionByIds->setOrder('most_viewed', $this->getCurrentDirection());
                    } elseif ($value == 'wished') {
                        $collectionByIds->setOrder('wished', $this->getCurrentDirection());
                    } elseif ($value == 'stockquantity') {
                        $joinConditions = 'e.entity_id = cataloginventory_stock_item.product_id';
                        $collectionByIds->getSelect()->join(
                            array('cataloginventory_stock_item'),
                            $joinConditions,
                            array()
                        )
                        ->order('cataloginventory_stock_item.qty '.$this->getCurrentDirection().' ');
                    } elseif ($value == 'top_rated') {
                        $joinConditions = 'e.entity_id = review_entity_summary.entity_pk_value';
                        $collectionByIds->getSelect()->join(
                            array('review_entity_summary'),
                            $joinConditions,
                            array()
                        )
                        ->where("review_entity_summary.store_id=".$this->storeManager->getStore()->getId()."")
                        ->order('review_entity_summary.rating_summary '.$this->getCurrentDirection().' ');
                    } elseif ($value == 'saving') {
                        $collectionByIds->setOrder('saving', $this->getCurrentDirection());
                    } elseif ($value == 'position') {
                        $collectionByIds->setOrder('position', $this->getCurrentDirection());
                    } elseif ($value == 'price') {
                        $collectionByIds->setOrder('price', $this->getCurrentDirection());
                    } elseif ($value == 'name') {
                        $collectionByIds->setOrder('name', $this->getCurrentDirection());
                    } elseif ($value == 'price_asc') {
                        $collectionByIds->setOrder('price', 'asc');
                    } elseif ($value == 'price_desc') {
                        $collectionByIds->setOrder('price', 'desc');
                    } else {
                        $collectionByIds->setOrder($value, $this->getCurrentDirection());
                    }
                }
            }

            $page = ($this->request->getParam('p')) ? $this->request->getParam('p') : 1;
            $pageSize = ($this->request->getParam('product_list_limit')) ? $this->request->getParam('product_list_limit') : 12;
            $collectionByIds->setCurPage($page);
            $collectionByIds->setPageSize($pageSize);
            return $collectionByIds;
        }

        return $this->_productCollection;
    }
/**
 * @param $childId
 * @return mixed
 */
    public function getParentId($childId)
    {
        /* for simple product of configurable product */
        $product = $this->configurable->getParentIdsByChild($childId);
        if (isset($product[0])) {
            return $product[0];
        }

        /* for bundle product of Bundle product */
        $bundle = $this->bundle->getParentIdsByChild($childId);
        if (!empty($bundle)) {
            return $bundle[0];
        }

        /* for grouped product of Group product */
        $grouped = $this->grouped->getParentIdsByChild($childId);
        if (!empty($grouped)) {
            return $grouped[0];
        }

        return $childId;
    }
/**
 * Get catalog layer model
 *
 * @return Layer
 */
    public function getLayer()
    {
        return $this->_catalogLayer;
    }
/**
 * Retrieve loaded category collection
 *
 * @return AbstractCollection
 */
    public function getLoadedProductCollection()
    {
        $collection = $this->_getProductCollection();
        $categoryId = $this->getLayer()->getCurrentCategory()->getId();
        foreach ($collection as $product) {
            $product->setData('category_id', $categoryId);
        }

        return $collection;
    }
/**
 * Retrieve current view mode
 *
 * @return string
 */
    public function getMode()
    {
        if ($this->getChildBlock('toolbar')) {
            return $this->getChildBlock('toolbar')->getCurrentMode();
        }

        return $this->getDefaultListingMode();
    }
/**
 * Get listing mode for products if toolbar is removed from layout.
 * Use the general configuration for product list mode from config path catalog/frontend/list_mode as default value
 * or mode data from block declaration from layout.
 *
 * @return string
 */
    private function getDefaultListingMode()
    {
    // default Toolbar when the toolbar layout is not used
        $defaultToolbar = $this->getToolbarBlock();
        $availableModes = $defaultToolbar->getModes();
    // layout config mode
        $mode = $this->getData('mode');
        if (!$mode || !isset($availableModes[$mode])) {
    // default config mode
            $mode = $defaultToolbar->getCurrentMode();
        }

        return $mode;
    }
/**
 * Need use as _prepareLayout - but problem in declaring collection from another block.
 * (was problem with search result)
 *
 * @return $this
 */
    protected function _beforeToHtml()
    {
        $collection = $this->_getProductCollection();
        $this->addToolbarBlock($collection);
        if (!$collection->isLoaded()) {
            $collection->load();
        }

        return parent::_beforeToHtml();
    }
/**
 * Add toolbar block from product listing layout
 *
 * @param Collection $collection
 */
    private function addToolbarBlock(Collection $collection)
    {
        $toolbarLayout = $this->getToolbarFromLayout();
        if ($toolbarLayout) {
            $this->configureToolbar($toolbarLayout, $collection);
        }
    }
/**
 * Retrieve Toolbar block from layout or a default Toolbar
 *
 * @return Toolbar
 */
    public function getToolbarBlock()
    {
        $block = $this->getToolbarFromLayout();
        if (!$block) {
            $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        }

        return $block;
    }
/**
 * Get toolbar block from layout
 *
 * @return bool|Toolbar
 */
    private function getToolbarFromLayout()
    {
        $blockName = $this->getToolbarBlockName();
        $toolbarLayout = false;
        if ($blockName) {
            $toolbarLayout = $this->getLayout()->getBlock($blockName);
        }

        return $toolbarLayout;
    }
/**
 * Retrieve additional blocks html
 *
 * @return string
 */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }
/**
 * Retrieve list toolbar HTML
 *
 * @return string
 */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
/**
 * Set collection.
 *
 * @param AbstractCollection $collection
 * @return $this
 */
    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }
/**
 * Add attribute.
 *
 * @param array|string|integer|Element $code
 * @return $this
 */
    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }
/**
 * Get price block template.
 *
 * @return mixed
 */
    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }
/**
 * Retrieve Catalog Config object
 *
 * @return Config
 */
    protected function _getConfig()
    {
        return $this->_catalogConfig;
    }
/**
 * Prepare Sort By fields from Category Data
 *
 * @param Category $category
 * @return $this
 */
    public function prepareSortableFieldsByCategory($category)
    {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }

        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            $categorySortBy = $this->getDefaultSortBy() ?: $category->getDefaultSortBy();
            if ($categorySortBy) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }

                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }
/**
 * Return identifiers for produced content
 *
 * @return array
 */
    public function getIdentities()
    {
        $identities = array();
        $category = $this->getLayer()->getCurrentCategory();
        if ($category) {
            $identities[] = array(Product::CACHE_PRODUCT_CATEGORY_TAG . '_' . $category->getId());
        }

    //Check if category page shows only static block (No products)
        if ($category->getData('display_mode') != Category::DM_PAGE) {
            foreach ($this->_getProductCollection() as $item) {
                $identities[] = $item->getIdentities();
            }
        }

        $identities = array_merge(array(), ...$identities);
        return $identities;
    }
/**
 * Get post parameters
 *
 * @param Product $product
 * @return array
 */
    public function getAddToCartPostParams(Product $product)
    {
        $url = $this->getAddToCartUrl($product, array('_escape' => false));
        return array(
        'action' => $url,
        'data' => array(
            'product' => (int) $product->getEntityId(),
            ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
        )
        );
    }
/**
 * Get product price.
 *
 * @param Product $product
 * @return string
 */
    public function getProductPrice(Product $product)
    {
        $priceRender = $this->getPriceRender();
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                array(
                'include_container' => true,
                'display_minimal_price' => true,
                'zone' => Render::ZONE_ITEM_LIST,
                'list_category_page' => true
                )
            );
        }

        return $price;
    }
/**
 * Specifies that price rendering should be done for the list of products.
 * (rendering happens in the scope of product list, but not single product)
 *
 * @return Render
 */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default')
        ->setData('is_product_list', true);
    }

    private function initializeProductCollection()
    {
        $layer = $this->getLayer();
        /* @var $layer Layer */
        if ($this->getShowRootCategory()) {
            $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
        }

    // if this is a product view page
        if ($this->_coreRegistry->registry('product')) {
    // get collection of categories this product is associated with
            $categories = $this->_coreRegistry->registry('product')
            ->getCategoryCollection()->setPage(1, 1)
            ->load();
    // if the product is associated with any category
            if ($categories->count()) {
    // show products from this category
                $this->setCategoryId(current($categories->getIterator())->getId());
            }
        }

        $origCategory = null;
        if ($this->getCategoryId()) {
            try {
                $category = $this->categoryRepository->get($this->getCategoryId());
            } catch (NoSuchEntityException $e) {
                $category = null;
            }

            if ($category) {
                $origCategory = $layer->getCurrentCategory();
                $layer->setCurrentCategory($category);
            }
        }

        $collection = $layer->getProductCollection();
        $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());
        if ($origCategory) {
            $layer->setCurrentCategory($origCategory);
        }

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            array('collection' => $collection)
        );
        return $collection;
    }
/**
 * Configures the Toolbar block with options from this block and configured product collection.
 *
 * The purpose of this method is the one-way sharing of different sorting related data
 * between this block, which is responsible for product list rendering,
 * and the Toolbar block, whose responsibility is a rendering of these options.
 *
 * @param ProductList\Toolbar $toolbar
 * @param Collection $collection
 * @return void
 */
    private function configureToolbar(Toolbar $toolbar, Collection $collection)
    {
    // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }

        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }

        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }

        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

    // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }
}
