<?php
namespace Magneto\AmastyCustomization\Plugin\Model\Layer;

use Amasty\Shopby\Model\Layer\FilterList;
use Magento\Framework\App\RequestInterface;

class Query {

	public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
		RequestInterface $request
	) {
		$this->objectManager = $objectManager;
		$this->request = $request;
	}

	public function afterGetAllFilters(
		FilterList $subject, $result, $layer
	) {
		$additionalFilters[] = $this->objectManager->create(
			\Magneto\AmastyCustomization\Model\Layer\Filter\Query::class,
			['layer' => $layer]
		);

		$additionalFilters[] = $this->objectManager->create(
			\Magneto\AmastyCustomization\Model\Layer\Filter\Category::class,
			['layer' => $layer]
		);

		if ($this->request->getControllerModule() == "Magento_CatalogSearch") {
			return $result;
		}

		$result = $this->insertAdditionalFilters($result, $additionalFilters);

		return $result;
	}

	/**
	 * @param $listStandartFilters
	 * @param $listAdditionalFilters
	 * @return array
	 */
	protected function insertAdditionalFilters($listStandartFilters, $listAdditionalFilters) {
		if (count($listAdditionalFilters) == 0) {
			return $listStandartFilters;
		}

		return array_merge($listStandartFilters, $listAdditionalFilters);
	}
}