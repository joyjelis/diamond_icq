<?php

namespace Magneto\TransferMethods\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Actions
 */
class Actions extends Column
{

    public $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['method_id'])) {
                    $viewUrlPath = 'transfer/methods/edit/';
                    $urlEntityParamName = 'method_id';
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                    $viewUrlPath,
                                    [
                                        $urlEntityParamName => $item['method_id']
                                    ]
                                ),
                            'label' => __('Edit')
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
