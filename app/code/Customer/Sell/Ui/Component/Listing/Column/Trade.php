<?php

namespace Customer\Sell\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Trade extends Column
{
    const ALT_FIELD = 'name';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ContextInterface      $context
     * @param UiComponentFactory    $uiComponentFactory
     * @param Image                 $imageHelper
     * @param UrlInterface          $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param array                 $components
     * @param array                 $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * 'Undocumented function'
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                switch ($item['trade']) {
                    case 1:
                        $class = 'minor';
                        $value = 'Yes';
                        break;
                    case 0:
                        $class = 'notice';
                        $value = 'No';
                        break;
                }
                $item['trade'] =  $value;
            }
        }

        return $dataSource;
    }
}
