<?php

namespace Customer\Sell\Ui\Component\Listing\Column;

use Customer\Sell\Helper\Status as SellStatus;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Status extends Column
{
    const ALT_FIELD = 'name';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Customer\Sell\Helper\Status
     */
    protected $statusHelper;

    /**
     * @param ContextInterface      $context
     * @param UiComponentFactory    $uiComponentFactory
     * @param Image                 $imageHelper
     * @param UrlInterface          $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param array                 $components
     * @param array                 $data
     * @param SellStatus            $statusHelper
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SellStatus $statusHelper,
        array $components = [],
        array $data = []
    ) {
        $this->statusHelper = $statusHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source for Status Column
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as &$item) {
                $options = $this->statusHelper->getOptions();
                if (isset($options[$item['status']])) {
                    $value = $options[$item['status']];
                    $item['status'] = $value;
                }
            }
        }
        return $dataSource;
    }
}
