<?php

declare(strict_types=1);

namespace Customer\Sell\Model\Sell;

use Magento\Framework\App\Request\DataPersistorInterface;
use Customer\Sell\Model\ResourceModel\Sell\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    const URLSTRUCTURE = 'sell-your-jewellery/sell/loadimage/load/';

    protected $loadedData;
    protected $collection;
    protected $dataPersistor;
    /**
     * @var \Customer\Sell\Model\FileInfo
     */
    protected $fileInfo;
    protected $backendUrl;
    protected $storeManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Customer\Sell\Model\FileInfo $fileInfo
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Customer\Sell\Model\FileInfo $fileInfo,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->fileInfo = $fileInfo;
        $this->backendUrl = $backendUrl;
    }

    /**
     * Get data for admin ui-component
     *
     * @return array
     */
    public function getData()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $itemData = $model->getData();
            $itemData['certificateimage'] = null;
            if (isset($itemData['certificateimage'])) {
                $imageName = explode('/', $itemData['certificateimage']);
                $itemData['certificateimage'] = [
                    [
                        'name' => $imageName[0],
                        'url' => $baseUrl . 'sell/certificateimage/' . $itemData['certificateimage'],
                    ],
                ];
            }

            $images = null;
            if (isset($itemData['image'])) {
                $imageName = explode(',', $itemData['image']);
                foreach ($imageName as $item) {
                    if ($item == '') {
                        continue;
                    }
                    $stat = $this->fileInfo->getStat($item);
                    $mime = $this->fileInfo->getMimeType($item);
                    $images[] = [
                        'name' => $item,
                        'url' =>  $baseUrl . \Customer\Sell\Model\FileInfo::ENTITY_MEDIA_PATH . '/' . $item,
                        'encodedURL' =>  $this->getUrl($item, $model),
                        'size' => isset($stat) ? $stat['size'] : 0,
                        'type' => $mime
                    ];
                }
            }
            $itemData['image'] = $images;
            $this->loadedData[$model->getSellId()] = $itemData;
        }
        $data = $this->dataPersistor->get('webepower_sell_sell');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getSellId()] = $model->getData();
            $this->dataPersistor->clear('webepower_sell_sell');
        }
        return $this->loadedData;
    }

    private function getEncodedText($item, $model): string
    {
        return base64_encode(json_encode([$item, $model->getSellId()]));
    }

    private function getUrl($item, $model): string
    {
        return $this->backendUrl->getUrl(DataProvider::URLSTRUCTURE . $this->getEncodedText($item, $model));
    }
}
