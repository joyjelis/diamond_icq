<?php

declare (strict_types = 1);

namespace Magneto\TransferMethods\Model;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magneto\TransferMethods\Model\ResourceModel\Methods\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

	protected $loadedData;
	protected $collection;
	protected $dataPersistor;

	/**
	 *
	 * @param string $name
	 * @param string $primaryFieldName
	 * @param string $requestFieldName
	 * @param CollectionFactory $collectionFactory
	 * @param DataPersistorInterface $dataPersistor
	 * @param array $meta
	 * @param array $data
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $collectionFactory,
		DataPersistorInterface $dataPersistor,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		array $meta = [],
		array $data = []
	) {
		$this->collection = $collectionFactory->create();
		$this->dataPersistor = $dataPersistor;
		$this->storeManager = $storeManager;
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
	}

	/**
	 * Get data for admin ui-component
	 *
	 * @return array
	 */
	public function getData() {
		if (isset($this->loadedData)) {
			return $this->loadedData;
		}

		$items = $this->collection->getItems();

		foreach ($items as $model) {
			$itemData = $model->getData();
			$this->loadedData[$model->getMethodId()] = $itemData;
		}

		return $this->loadedData;
	}
}
