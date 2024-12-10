<?php
namespace Magneto\IncludePriceReceipt\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class IncludePriceReceipt extends Column
{
    protected $orderRepository;
    protected $searchCriteria;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        array $components = [],
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteria  = $criteria;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $order  = $this->orderRepository->get($item["entity_id"]);
                $status = $order->getData("include_price_in_receipt");

                switch ($status) {
                    case "0":
                        $include_price_in_receipt = "No";
                        break;
                    case "1":
                        $include_price_in_receipt = "Yes";
                        break;
                    default:
                        $include_price_in_receipt = "Failed";
                        break;
                }

                $item[$this->getData('name')] = $include_price_in_receipt;
            }
        }

        return $dataSource;
    }
}
