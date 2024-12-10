<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

declare (strict_types = 1);

namespace Magneto\AmastyCustomization\Model\Layer\Filter;

use Amasty\ShopbyBase\Model\Category\Manager as CategoryManager;
use Amasty\Shopby\Helper\Category as CategoryHelper;
use Amasty\Shopby\Helper\Data as ShopbyHelper;
use Amasty\Shopby\Model\Layer\Filter\Item\CategoryExtendedDataBuilder;
use Amasty\Shopby\Model\Layer\Filter\Resolver\FilterRequestDataResolver;
use Amasty\Shopby\Model\Layer\Filter\Resolver\FilterSettingResolver;
use Amasty\Shopby\Model\ResourceModel\Fulltext\Collection as ShopbyFulltextCollection;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Layer\Filter\DataProvider\Category as CategoryDataProvider;
use Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory as CategoryDataProviderFactory;
use Magento\Catalog\Model\Layer\Filter\ItemFactory as FilterItemFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Search\Api\SearchInterface;
use Magento\Store\Model\StoreManagerInterface;

class Category extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter {

	const MIN_CATEGORY_DEPTH = 1;

	const DENY_PERMISSION = '-2';

	const FILTER_FIELD = 'category';

	const EXCLUDE_CATEGORY_FROM_FILTER = 'am_exclude_from_filter';

	const TRUE = 1;

	/**
	 * @var Escaper
	 */
	private $escaper;

	/**
	 * @var CategoryDataProvider
	 */
	private $dataProvider;

	/**
	 * @var CategoryManager
	 */
	private $categoryManager;

	/**
	 * @var CategoryRepositoryInterface
	 */
	private $categoryRepository;

	/**
	 * @var Item\CategoryExtendedDataBuilder
	 */
	private $categoryExtendedDataBuilder;

	/**
	 * @var ShopbyHelper
	 */
	private $helper;

	/**
	 * @var CategoryHelper
	 */
	private $categoryHelper;

	/**
	 * @var SearchInterface
	 */
	private $search;

	/**
	 * @var MessageManager
	 */
	private $messageManager;

	/**
	 * @var ProductMetadataInterface
	 */
	private $productMetadata;

	/**
	 * @var FilterRequestDataResolver
	 */
	private $filterRequestDataResolver;

	/**
	 * @var FilterSettingResolver
	 */
	private $filterSettingResolver;

	/**
	 * @var CategoryCollectionFactory
	 */
	private $categoryCollectionFactory;

	public function __construct(
		FilterItemFactory $filterItemFactory,
		StoreManagerInterface $storeManager,
		Layer $layer,
		DataBuilder $itemDataBuilder,
		Escaper $escaper,
		CategoryFactory $categoryFactory,
		CategoryDataProviderFactory $categoryDataProviderFactory,
		CategoryManager $categoryManager,
		CategoryRepositoryInterface $categoryRepository,
		CategoryExtendedDataBuilder $categoryExtendedDataBuilder,
		ShopbyHelper $helper,
		CategoryHelper $categoryHelper,
		SearchInterface $search,
		MessageManager $messageManager,
		ProductMetadataInterface $productMetadata,
		FilterRequestDataResolver $filterRequestDataResolver,
		FilterSettingResolver $filterSettingResolver,
		CategoryCollectionFactory $categoryCollectionFactory,
		\Amasty\Shopby\Helper\Config $config,
		array $data = []
	) {
		parent::__construct(
			$filterItemFactory,
			$storeManager,
			$layer,
			$itemDataBuilder,
			$data
		);
		$this->categoryFactory = $categoryFactory;
		$this->helper = $helper;
		$this->escaper = $escaper;
		$this->_requestVar = 'cat';
		$this->config = $config;
		$this->dataProvider = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
		$this->categoryManager = $categoryManager;
		$this->categoryRepository = $categoryRepository;
		$this->categoryExtendedDataBuilder = $categoryExtendedDataBuilder;
		$this->categoryHelper = $categoryHelper;
		$this->search = $search;
		$this->messageManager = $messageManager;
		$this->productMetadata = $productMetadata;
		$this->filterRequestDataResolver = $filterRequestDataResolver;
		$this->filterSettingResolver = $filterSettingResolver;
		$this->categoryCollectionFactory = $categoryCollectionFactory;
	}

	/**
	 * Apply category filter to product collection
	 *
	 * @param   RequestInterface $request
	 * @return  $this
	 */
	public function apply(RequestInterface $request) {
		if ($this->config->isCategoryFilterEnabled()) {
			return $this;
		}

		if ($this->filterRequestDataResolver->isApplied($this)) {
			return $this;
		}
		$categoryId = $this->filterRequestDataResolver->getFilterParam($this) ?: $request->getParam('cat');
		if (empty($categoryId)) {
			return $this;
		}

		$categoryIds = explode(',', $categoryId);
		$categoryIds = array_unique($categoryIds);
		$category = $this->dataProvider->getCategory();

		if ($this->isMultiselect() && $request->getParam('cat') == $categoryId) {
			$categoryIds = $this->excludeCategoriesFromFilter($categoryIds);
			if (empty($categoryIds)) {
				return $this;
			}

			$this->filterRequestDataResolver->setCurrentValue($this, $categoryIds);
			$child = $category->getCollection()
				->addFieldToFilter($category->getIdFieldName(), ['in' => $categoryIds])
				->addAttributeToSelect('name');
			$categoriesInState = [];
			foreach ($categoryIds as $categoryId) {
				if ($currentCategory = $child->getItemById($categoryId)) {
					$categoriesInState[$currentCategory->getId()] = $currentCategory->getName();
				}
			}
			foreach ($categoriesInState as $key => $category) {
				$state = $this->_createItem($category, $key);
				$this->getLayer()->getState()->addFilter($state);
			}
		} else {
			$this->filterRequestDataResolver->setCurrentValue($this, $categoryIds);
			$this->dataProvider->setCategoryId($categoryId);
			if ($request->getParam('cat') != $category->getId() && $this->dataProvider->isValid()) {
				$this->getLayer()->getState()->addFilter(
					$this->_createItem(
						$this->dataProvider->getCategory()->getName(),
						$categoryId
					)
				);
			}
		}
		
		/** @var ShopbyFulltextCollection $productCollection */
		foreach ($categoryIds as $categoryId) {
			$productCollection = $this->getLayer()->getProductCollection()->addCategoryFilter($this->categoryFactory->create()->load($categoryId));
		}

		return $this;
	}

	public function isMultiselect(): bool {
		return true;
	}

	private function excludeCategoriesFromFilter(array $categoryIds): array
	{
		$collection = $this->getExcludedCategoryCollection();
		$excluded = $collection ? $collection->getColumnValues($collection->getEntity()->getIdFieldName()) : [];

		return array_values(array_diff($categoryIds, $excluded));
	}

	/**
	 * Get filter name
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getName() {
		return __('Category');
	}
}
