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
namespace FME\ProductSorting\Block\Product\ProductList;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
     /**
      * Products collection
      *
      * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
      */
    protected $_collection = null;

    /**
     * List of available order fields
     *
     * @var array
     */
    protected $_availableOrder = null;

    /**
     * List of available view types
     *
     * @var array
     */
    protected $_availableMode = array();

    /**
     * Is enable View switcher
     *
     * @var bool
     */
    protected $_enableViewSwitcher = true;

    /**
     * Is Expanded
     *
     * @var bool
     */
    protected $_isExpanded = true;

    /**
     * Default Order field
     *
     * @var string
     */
    protected $_orderField = null;

    /**
     * Default direction
     *
     * @var string
     */
    protected $_direction = ProductList::DEFAULT_SORT_DIRECTION;

    /**
     * Default View mode
     *
     * @var string
     */
    protected $_viewMode = null;

    /**
     * @var bool $_paramsMemorizeAllowed
     * @deprecated 103.0.1
     */
    protected $_paramsMemorizeAllowed = true;

    /**
     * @var string
     */
    protected $_template = 'Magento_Catalog::product/list/toolbar.phtml';

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     * @deprecated 103.0.1
     */
    protected $_catalogSession;

    /**
     * @var ToolbarModel
     */
    protected $_toolbarModel;
    protected $helpers;
    /**
     * @var ToolbarMemorizer
     */
    private $toolbarMemorizer;

    /**
     * @var ProductList
     */
    protected $_productListHelper;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;
    protected $_date;
    /**
     * Catalog layer
     *
     * @var Layer
     */
    protected $_catalogLayer;
    protected $_categoryFactory;
    protected $resourceModel;
    // const $count =0;
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;
    protected $request;
    protected $_customerSession;
   // protected $_prodCollection;
    protected $productStatus;
    protected $productVisibility;
    protected $_productCollectionFactory;
    protected $wishlist;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Resolver $layerResolver
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param ToolbarModel $toolbarModel
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param ProductList $productListHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param array $data
     * @param ToolbarMemorizer|null $toolbarMemorizer
     * @param \Magento\Framework\App\Http\Context|null $httpContext
     * @param \Magento\Framework\Data\Form\FormKey|null $formKey
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Resolver $layerResolver,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        ToolbarModel $toolbarModel,
        \FME\ProductSorting\Model\ResourceModel\Filters $resourceModel,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        ProductList $productListHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        ToolbarMemorizer $toolbarMemorizer = null,
        \Magento\Framework\App\Http\Context $httpContext = null,
        \Magento\Framework\App\Request\Http $request,
        \FME\ProductSorting\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        // \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_prodCollection,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Framework\Data\Form\FormKey $formKey = null,
        array $data = array()
    ) {
          // $this->_prodCollection = $_prodCollection;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->_productCollectionFactory = $productFactory;
        $this->_catalogLayer = $layerResolver->get();
        $this->_catalogSession = $catalogSession;
        $this->request = $request;
        $this->helpers = $helper;
        $this->resourceModel = $resourceModel;
        $this->_categoryFactory = $categoryFactory;
        $this->_catalogConfig = $catalogConfig;
        $this->_toolbarModel = $toolbarModel;
        $this->_date = $date;
        $this->_customerSession = $customerSession;
        $this->wishlist = $wishlist;
        $this->urlEncoder = $urlEncoder;
        $this->_productListHelper = $productListHelper;
        $this->_postDataHelper = $postDataHelper;
        $this->toolbarMemorizer = $toolbarMemorizer ?: ObjectManager::getInstance()->get(
            ToolbarMemorizer::class
        );
        $this->httpContext = $httpContext ?: ObjectManager::getInstance()->get(
            \Magento\Framework\App\Http\Context::class
        );
        $this->formKey = $formKey ?: ObjectManager::getInstance()->get(
            \Magento\Framework\Data\Form\FormKey::class
        );
        parent::__construct(
            $context,
            $catalogSession,
            $catalogConfig,
            $toolbarModel,
            $urlEncoder,
            $productListHelper,
            $postDataHelper
        );
    }

    /**
     * Disable list state params memorizing
     *
     * @return $this
     * @deprecated 103.0.1
     */
    public function disableParamsMemorizing()
    {
        $this->_paramsMemorizeAllowed = false;
        return $this;
    }

    /**
     * Memorize parameter value for session
     *
     * @param string $param parameter name
     * @param mixed $value parameter value
     * @return $this
     * @deprecated 103.0.1
     */
    protected function _memorizeParam($param, $value)
    {
        if ($this->_paramsMemorizeAllowed && !$this->_catalogSession->getParamsMemorizeDisabled()) {
            $this->_catalogSession->setData($param, $value);
        }

        return $this;
    }
    /**
     * Set collection to sorting option
     *
     * @param \Magento\Framework\Data\Collection $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {

        $this->_collection = $collection;
        $outOfStock = $this->helpers->getConfig('general/stock_status');
        $noImage = $this->helpers->getConfig('general/stock_qty');

        if ($this->getCurrentOrder()) {
            switch ($this->getCurrentOrder()) {
                case 'created_at':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('created_at', $this->getCurrentDirection());
                    break;
                case 'best_seller':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('status_order', $this->getCurrentDirection());

                    break;
                case 'review_count':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('review_count', $this->getCurrentDirection());

                    break;
                case 'top_rated':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('top_rated', $this->getCurrentDirection());

                    break;
                case 'most_viewed':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('most_viewed', $this->getCurrentDirection());

                    break;

                case 'wished':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $wishlist =  $this->helpers->getCustomerId();
                    if ($wishlist) {
                        $this->helpers->getWishlistProduct($wishlist);
                    }
                
                    $this->_collection->setOrder('wished', $this->getCurrentDirection());

                    break;
                case 'stockquantity':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('stockquantity', $this->getCurrentDirection());

                    break;
                case 'saving':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('saving', $this->getCurrentDirection());

                    break;
                case 'position':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('position', $this->getCurrentDirection());

                    break;
                case 'price':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('price', $this->getCurrentDirection());

                    break;
                case 'name':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('name', $this->getCurrentDirection());

                    break;
                case 'price_asc':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('price', 'asc');

                    break;
                case 'price_desc':
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder('price', 'desc');

                    break;

                default:
                    if ($noImage) {
                        $this->_collection->setOrder('imageProduct', 'desc');
                    }

                    if ($outOfStock) {
                        $this->_collection->setOrder('outOfStock', 'desc');
                    }

                    $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
                    break;
            }
        }

        $this->_collection->setCurPage($this->getCurrentPage());

        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }

        return $this;
    }

    public function checkCurrentOrder()
    {
        return $this->getCurrentOrder();
    }

     /**
      * Return products collection instance
      *
      * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
      */
    public function getCollection()
    {

        return $this->_collection;

        $param = $this->request->getParams();
        $outOfStock = $this->helpers->getConfig('general/stock_status');
        $noImage = $this->helpers->getConfig('general/stock_qty');


        $productsCollection = $this->_productCollectionFactory->create();
        $collectionByIds = $productsCollection->addAttributeToSelect('*');
        $category = $this->_categoryFactory->create()->load($this->request->getParam('id'));
        $collectionByIds->addCategoryFilter($category);
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

            if ($key == 'product_list_order') {
                if ($this->getCurrentOrder()) {
                    switch ($this->getCurrentOrder()) {
                        case 'created_at':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('created_at', $this->getCurrentDirection());
                            break;
                        case 'best_seller':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('status_order', $this->getCurrentDirection());

                            break;
                        case 'review_count':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('review_count', $this->getCurrentDirection());

                            break;
                        case 'top_rated':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('top_rated', $this->getCurrentDirection());

                            break;
                        case 'most_viewed':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('most_viewed', $this->getCurrentDirection());

                            break;

                        case 'wished':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $wishlist =  $this->helpers->getCustomerId();
                            if ($wishlist) {
                                $this->helpers->getWishlistProduct($wishlist);
                            }

                            $collectionByIds->setOrder('wished', $this->getCurrentDirection());
                            break;
                        case 'stockquantity':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('stockquantity', $this->getCurrentDirection());
                            break;
                        case 'saving':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('saving', $this->getCurrentDirection());
                            break;
                        case 'position':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('position', $this->getCurrentDirection());
                            break;
                        case 'price':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('price', $this->getCurrentDirection());
                            break;
                        case 'name':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('name', $this->getCurrentDirection());
                            break;
                        case 'price_asc':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('price', 'asc');
                            break;
                        case 'price_desc':
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder('price', 'desc');
                            break;
                        default:
                            if ($noImage) {
                                $collectionByIds->setOrder('imageProduct', 'desc');
                            }

                            if ($outOfStock) {
                                $collectionByIds->setOrder('outOfStock', 'desc');
                            }

                            $collectionByIds->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
                            break;
                    }
                }
            }
        }

        $collectionByIds->addAttributeToFilter('status', array('in' => $this->productStatus->getVisibleStatusIds()));
        $collectionByIds->setVisibility($this->productVisibility->getVisibleInSiteIds());
        return $collectionByIds;
    }

    /**
     * Return current page from request
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->_toolbarModel->getCurrentPage();
    }

    /**
     * Get grid products sort order field
     *
     * @return string
     */
    public function getCurrentOrder()
    {
        $order = $this->_getData('_current_grid_order');
        if ($order) {
            return $order;
        }

        $orders = $this->getAvailableOrders();
        $defaultOrder = $this->getOrderField();

        if (!isset($orders[$defaultOrder])) {
            $keys = array_keys($orders);
            $defaultOrder = $keys[0];
        }

        $order = $this->toolbarMemorizer->getOrder();
        if (!$order || !isset($orders[$order])) {
            $order = $defaultOrder;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::ORDER_PARAM_NAME, $order, $defaultOrder);
        }

        $this->setData('_current_grid_order', $order);
        return $order;
    }

    /**
     * Return Reverse direction of current direction
     *
     * @return string
     */
    public function getCurrentDirectionReverse()
    {
        if ($this->getCurrentDirection() == 'asc') {
            return 'desc';
        } elseif ($this->getCurrentDirection() == 'desc') {
            return 'asc';
        } else {
            return $this->getCurrentDirection();
        }
    }
     /**
      * Set default Order field
      *
      * @param string $field
      * @return $this
      */
    public function setDefaultOrder($field)
    {
        $enable =  $this->helpers->getConfig('general/enabled');
        // $enableCategory =  $this->helpers->getConfig('sorting_default/enabled');
        // $enableResult =  $this->helpers->getConfig('sorting_default/enabled');
        if ($enable) {
            $controller = $this->request->getControllerName();
            if ($controller=='category') {
                $sortVal = $this->helpers->getConfig('sorting_default/category_sort');
                if ($sortVal) {
                    $field = $sortVal;
                }
            } else {
                $sortVal = $this->helpers->getConfig('sorting_default/search_sort');
                if ($sortVal) {
                    $field = $sortVal;
                }
            }
        }


        $this->loadAvailableOrders();
        if (isset($this->_availableOrder[$field])) {
            $this->_orderField = $field;
        }

        return $this;
    }

    /**
     * Set default sort direction
     *
     * @param string $dir
     * @return $this
     */
    public function setDefaultDirection($dir)
    {
        if (in_array(strtolower($dir), array('asc', 'desc'))) {
            $this->_direction = strtolower($dir);
        }

        return $this;
    }

    /**
     * Retrieve available Order fields list
     *
     * @return array
     */
    public function getAvailableOrders()
    {
        $this->loadAvailableOrders();
        return $this->_availableOrder;
    }

    /**
     * Set Available order fields list
     *
     * @param array $orders
     * @return $this
     */
    public function setAvailableOrders($orders)
    {
        $this->_availableOrder = $orders;
        return $this;
    }

    /**
     * Add order to available orders
     *
     * @param string $order
     * @param string $value
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function addOrderToAvailableOrders($order, $value)
    {
        $this->loadAvailableOrders();
        $this->_availableOrder[$order] = $value;
        return $this;
    }

    /**
     * Remove order from available orders if exists
     *
     * @param string $order
     * @return $this
     */
    public function removeOrderFromAvailableOrders($order)
    {
        $this->loadAvailableOrders();
        if (isset($this->_availableOrder[$order])) {
            unset($this->_availableOrder[$order]);
        }

        return $this;
    }

    /**
     * Compare defined order field with current order field
     *
     * @param string $order
     * @return bool
     */
    public function isOrderCurrent($order)
    {
        return $order == $this->getCurrentOrder();
    }

    /**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params = array())
    {
        $urlParams = array();
        $urlParams['_current'] = true;
        $urlParams['_escape'] = false;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }

    /**
     * Get pager encoded url.
     *
     * @param array $params
     * @return string
     */
    public function getPagerEncodedUrl($params = array())
    {
        return $this->urlEncoder->encode($this->getPagerUrl($params));
    }

    /**
     * Retrieve current View mode
     *
     * @return string
     */
    public function getCurrentMode()
    {
        $mode = $this->_getData('_current_grid_mode');
        if ($mode) {
            return $mode;
        }

        $defaultMode = $this->_productListHelper->getDefaultViewMode($this->getModes());
        $mode = $this->toolbarMemorizer->getMode();
        if (!$mode || !isset($this->_availableMode[$mode])) {
            $mode = $defaultMode;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::MODE_PARAM_NAME, $mode, $defaultMode);
        }

        $this->setData('_current_grid_mode', $mode);
        return $mode;
    }

    /**
     * Compare defined view mode with current active mode
     *
     * @param string $mode
     * @return bool
     */
    public function isModeActive($mode)
    {
        return $this->getCurrentMode() == $mode;
    }

    /**
     * Retrieve available view modes
     *
     * @return array
     */
    public function getModes()
    {
        if ($this->_availableMode === array()) {
            $this->_availableMode = $this->_productListHelper->getAvailableViewMode();
        }

        return $this->_availableMode;
    }

    /**
     * Set available view modes list
     *
     * @param array $modes
     * @return $this
     */
    public function setModes($modes)
    {
        $this->getModes();
        if (!isset($this->_availableMode)) {
            $this->_availableMode = $modes;
        }

        return $this;
    }

    /**
     * Disable view switcher
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function disableViewSwitcher()
    {
        $this->_enableViewSwitcher = false;
        return $this;
    }

    /**
     * Enable view switcher
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function enableViewSwitcher()
    {
        $this->_enableViewSwitcher = true;
        return $this;
    }

    /**
     * Is a enabled view switcher
     *
     * @return bool
     */
    public function isEnabledViewSwitcher()
    {
        return $this->_enableViewSwitcher;
    }

    /**
     * Disable Expanded
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function disableExpanded()
    {
        $this->_isExpanded = false;
        return $this;
    }

    /**
     * Enable Expanded
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function enableExpanded()
    {
        $this->_isExpanded = true;
        return $this;
    }

    /**
     * Check is Expanded
     *
     * @return bool
     */
    public function isExpanded()
    {
        return $this->_isExpanded;
    }

    /**
     * Retrieve default per page values
     *
     * @return string (comma separated)
     */
    public function getDefaultPerPageValue()
    {
        if ($this->getCurrentMode() == 'list' && ($default = $this->getDefaultListPerPage())) {
            return $default;
        } elseif ($this->getCurrentMode() == 'grid' && ($default = $this->getDefaultGridPerPage())) {
            return $default;
        }

        return $this->_productListHelper->getDefaultLimitPerPageValue($this->getCurrentMode());
    }

    /**
     * Retrieve available limits for current view mode
     *
     * @return array
     */
    public function getAvailableLimit()
    {
        return $this->_productListHelper->getAvailableLimit($this->getCurrentMode());
    }

    /**
     * Get specified products limit display per page
     *
     * @return string
     */
    public function getLimit()
    {
        $limit = $this->_getData('_current_limit');
        if ($limit) {
            return $limit;
        }

        $limits = $this->getAvailableLimit();
        $defaultLimit = $this->getDefaultPerPageValue();
        if (!$defaultLimit || !isset($limits[$defaultLimit])) {
            $keys = array_keys($limits);
            $defaultLimit = $keys[0];
        }

        $limit = $this->toolbarMemorizer->getLimit();
        if (!$limit || !isset($limits[$limit])) {
            $limit = $defaultLimit;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::LIMIT_PARAM_NAME, $limit, $defaultLimit);
        }

        $this->setData('_current_limit', $limit);
        return $limit;
    }

    /**
     * Check if limit is current used in toolbar.
     *
     * @param int $limit
     * @return bool
     */
    public function isLimitCurrent($limit)
    {
        return $limit == $this->getLimit();
    }

    /**
     * Pager number of items from which products started on current page.
     *
     * @return int
     */
    public function getFirstNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize() * ($collection->getCurPage() - 1) + 1;
    }

    /**
     * Pager number of items products finished on current page.
     *
     * @return int
     */
    public function getLastNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize() * ($collection->getCurPage() - 1) + $collection->count();
    }

    /**
     * Total number of products in current category.
     *
     * @return int
     */
    public function getTotalNum()
    {
        return $this->getCollection()->getSize();
    }

    /**
     * Check if current page is the first.
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getCollection()->getCurPage() == 1;
    }

    /**
     * Return last page number.
     *
     * @return int
     */
    public function getLastPageNum()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('product_list_toolbar_pager');

        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );

            return $pagerBlock->toHtml();
        }

        return '';
    }

    /**
     * Retrieve widget options in json format
     *
     * @param array $customOptions Optional parameter for passing custom selectors from template
     * @return string
     */
    public function getWidgetOptionsJson(array $customOptions = array())
    {
        $defaultMode = $this->_productListHelper->getDefaultViewMode($this->getModes());
        $options = array(
            'mode' => ToolbarModel::MODE_PARAM_NAME,
            'direction' => ToolbarModel::DIRECTION_PARAM_NAME,
            'order' => ToolbarModel::ORDER_PARAM_NAME,
            'limit' => ToolbarModel::LIMIT_PARAM_NAME,
            'modeDefault' => $defaultMode,
            'directionDefault' => $this->_direction ?: ProductList::DEFAULT_SORT_DIRECTION,
            'orderDefault' => $this->getOrderField(),
            'limitDefault' => $this->_productListHelper->getDefaultLimitPerPageValue($defaultMode),
            'url' => $this->getPagerUrl(),
            'formKey' => $this->formKey->getFormKey(),
            'post' => $this->toolbarMemorizer->isMemorizingAllowed() ? true : false
        );
        $options = array_replace_recursive($options, $customOptions);
        return json_encode(array('productListToolbarForm' => $options));
    }

    /**
     * Get order field
     *
     * @return null|string
     */
    protected function getOrderField()
    {
        if ($this->_orderField === null) {
            $this->_orderField = $this->_productListHelper->getDefaultSortField();
        }

        return $this->_orderField;
    }

    /**
     * Load Available Orders
     *
     * @return $this
     */
    private function loadAvailableOrders()
    {
        if ($this->_availableOrder === null) {
            $this->_availableOrder = $this->_catalogConfig->getAttributeUsedForSortByArray();
        }

        return $this;
    }
}
