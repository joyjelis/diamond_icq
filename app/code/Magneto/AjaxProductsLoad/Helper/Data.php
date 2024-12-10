<?php

namespace Magneto\AjaxProductsLoad\Helper;

use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{

    const XML_IS_ENABLE = "ajax_product_load/general/enabled";

    const XML_DESK_LIMIT = "ajax_product_load/general/desktop_limit";

    const XML_MOBILE_LIMIT = "ajax_product_load/general/mobile_limit";

    public $collection_size = 0;

    /**
     * _Construct
     *
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param ImageFactory $imageHelperFactory
     * @param CategoryRepository $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param BlockFactory $blockFactory
     * @param priceHelper $priceHelper
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        ImageFactory $imageHelperFactory,
        CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager,
        BlockFactory $blockFactory,
        Status $productStatus,
        Visibility $productVisibility,
        priceHelper $priceHelper
    ) {
        parent::__construct($context);

        // $writer = new \Leminas\Log\Writer\Stream(BP . '/var/log/test.log');
        // $logger = new \Leminas\Log\Logger();
        // $logger->addWriter($writer);
        // $this->logger = $logger;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->categoryRepository = $categoryRepository;
        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->priceHelper = $priceHelper;
        $this->blockFactory = $blockFactory;
    }

    public function getCollectionSize()
    {
        return $this->collection_size;
    }

    public function setCollectionSize($size)
    {
        $this->collection_size = $size;
    }

    /**
     * Load Category
     *
     * @param int $categoryId
     * @return \Magento\Category\Model\Category
     */
    public function loadCategory($categoryId)
    {
        try {
            return $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (\Exception $e) {
            // $this->logger->info($e->getMessage());
        }
    }

    /**
     * Get Category
     *
     * @param int $categoryId
     * @return \Magento\Category\Model\Category
     */
    public function getCategory($categoryId)
    {
        return $this->loadCategory($categoryId);
    }

    /**
     * Product Html Builder
     *
     * @param array $categoryIds
     * @return string
     */
    public function getProducts($categoryIds, $limit = 12)
    {
        if (!empty($categoryIds)) {
            $collection = $this->getProductCollectionByCategories($categoryIds, $limit);
            $this->setCollectionSize($collection->count());
            return $this->getHtml($collection);
        }
    }

    /**
     * Get Sidebar Categories
     *
     * @param \Magento\Category\Model\Category $category
     * @return array
     */
    public function getSidebarCategories($category)
    {
        if (!empty($category)) {
            if (is_string($category)) {
                $sidebarcategories = explode(",", $category);
                if (!empty($sidebarcategories)) {
                    $childcategories = [];
                    foreach ($sidebarcategories as $key => $categoryId) {
                        $category = $this->loadCategory($categoryId);
                        if (!empty($category)) {
                            if (!isset($childcategories['active'])) {
                                $childcategories['active'] = $category->getId();
                            }

                            $childcategories['category'][] = [
                                'id' => $category->getId(),
                                'name' => $category->getName(),
                                'url_key' => $category->getUrl(),
                            ];
                        }
                    }

                    return $childcategories;
                }
            } else {
                $childcategories = [];
                foreach ($category->getChildrenCategories() as $child) {
                    if (!isset($childcategories['active'])) {
                        $childcategories['active'] = $child->getId();
                    }

                    $childcategories['category'][] = [
                        'id' => $child->getId(),
                        'name' => $child->getName(),
                        'url_key' => $child->getUrl(),
                    ];
                }

                return $childcategories;
            }
        }

        return $childcategories = [
            'category' => [],
            'active' => null
        ];
    }

    /**
     * Get Filter Categories
     *
     * @param array $categoryids
     * @return array
     */
    public function getFilterCategories($categoryids)
    {
        $filtercategory = [];
        if (!empty($categoryids)) {
            $filtercategory = explode(",", $categoryids);
        }

        if (!empty($filtercategory)) {
            $childcategories = [];
            foreach ($filtercategory as $key => $categoryId) {
                $category = $this->loadCategory($categoryId);
                if (!empty($category)) {
                    if (!isset($childcategories['active'])) {
                        $childcategories['active'] = $category->getId();
                        $childcategories['active'] = "";
                    }

                    $childcategories['category'][] = [
                        'id' => $category->getId(),
                        'name' => $category->getName(),
                        'url_key' => $category->getUrl(),
                    ];
                }
            }

            return $childcategories;
        }

        return $childcategories = [
            'category' => [],
            'active' => null
        ];
    }

    /**
     * Check Module Enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getConfig(self::XML_IS_ENABLE);
    }

    /**
     * Check Desktop Limit
     *
     * @return int
     */
    public function getDesktopLimit()
    {
        return $this->getConfig(self::XML_DESK_LIMIT);
    }

    /**
     * Check Mobile Limit
     *
     * @return int
     */
    public function getMobileLimit()
    {
        return $this->getConfig(self::XML_MOBILE_LIMIT);
    }

    /**
     * Get Product Collcation
     *
     * @param array $categories
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollectionByCategories($categories, $limit)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->setFlag('has_stock_status_filter', true);
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setOrder('position', 'asc');
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        $collection->setPageSize($limit)->setCurPage(1);

        if (is_array($categories)) {
            foreach ($categories as $key => $id) {
                $collection->joinField('category_id_' . $id, 'catalog_category_product', 'category_id', 'product_id=entity_id', null, 'left');
                $collection->addAttributeToFilter('category_id_' . $id, ['eq' => $id]);
            }
        }

        return $collection;
    }

    /**
     * Convert Collcetion to Html
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return string
     */
    public function getHtml($collection)
    {
        $block = $this->blockFactory->createBlock(\Magneto\AjaxProductsLoad\Block\ListProduct::class);
        $block->setTemplate($block->getTemplate());
        $block->setData('collection', $collection);
        $html = $block->toHtml();
        return $html;
    }

    /**
     * get Product Image
     *
     * @param \Magento\Product\Model\Product $product
     * @return string
     */
    public function getProductImage($product)
    {
        $imageUrl = $this->imageHelperFactory->create()
            ->init($product, 'home_jewellery_image')->getUrl();
        return $imageUrl;
    }

    /**
     * Get Config values
     *
     * @param string $config
     * @return string
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
