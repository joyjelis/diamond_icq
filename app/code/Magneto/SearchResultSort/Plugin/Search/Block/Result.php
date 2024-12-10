<?php
namespace Magneto\SearchResultSort\Plugin\Search\Block;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context as Context;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\View\Element\Template as ViewTemplate;
use Magento\Search\Model\QueryFactory;

class Result extends ViewTemplate
{
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        QueryFactory $queryFactory,
        array $data =[]
    )
    {
        $this->_catalogLayer = $layerResolver->get();
        $this->_queryFactory = $queryFactory;

        parent::__construct($context, $data);
    }

    public function afterSetListOrders() {

        $category = $this->_catalogLayer->getCurrentCategory();
        /* @var $category \Magento\Catalog\Model\Category */
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders['relevance'] = __('Relevance');

        $this->getLayout()->getBlock('search_result_list')->setAvailableOrders(
            $availableOrders
        )->setDefaultDirection(
            'asc'
        )->setDefaultSortBy(
            'relevance'
        );

        return $this;

    }
}