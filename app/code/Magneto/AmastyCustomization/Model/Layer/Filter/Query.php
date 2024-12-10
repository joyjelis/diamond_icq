<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

declare (strict_types = 1);

namespace Magneto\AmastyCustomization\Model\Layer\Filter;

use Amasty\Shopby\Model\Layer\Filter\Resolver\FilterRequestDataResolver;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder as ItemDataBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Search\Api\SearchInterface;
use Magento\Store\Model\StoreManagerInterface;

class Query extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter {

	const ATTRIBUTE_CODE = 'q';

	/**
	 * @var ScopeConfigInterface
	 */
	private $scopeConfig;

	/**
	 * @var SearchInterface
	 */
	private $search;

	/**
	 * @var FilterRequestDataResolver
	 */
	private $filterRequestDataResolver;

	public function __construct(
		ItemFactory $filterItemFactory,
		StoreManagerInterface $storeManager,
		Layer $layer,
		ItemDataBuilder $itemDataBuilder,
		ScopeConfigInterface $scopeConfig,
		SearchInterface $search,
		FilterRequestDataResolver $filterRequestDataResolver,
		array $data = []
	) {
		parent::__construct(
			$filterItemFactory,
			$storeManager,
			$layer,
			$itemDataBuilder,
			$data
		);

		$this->_requestVar = self::ATTRIBUTE_CODE;
		$this->scopeConfig = $scopeConfig;
		$this->search = $search;
		$this->filterRequestDataResolver = $filterRequestDataResolver;
	}

	/**
	 * @param RequestInterface $request
	 *
	 * @return $this
	 */
	public function apply(RequestInterface $request) {

		if ($this->filterRequestDataResolver->isApplied($this)) {
			return $this;
		}

		$filter = $this->filterRequestDataResolver->getFilterParam($this);

		if (!empty($filter)) {
			$this->filterRequestDataResolver->setCurrentValue($this, $filter);
			$name = __("Search: ") . $filter;
			$this->getLayer()->getState()->addFilter($this->_createItem($name, $filter));
		}

		return $this;
	}

	/**
	 * Get filter name
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getName() {
		return __("Search");
	}
}
