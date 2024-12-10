<?php

namespace Customer\Sell\Block\Quote;

use Magento\Framework\View\Element\Template;

class Filter extends Template
{

    const DEFUALT_VIEW = "listview";

    const DEFUALT_SORT = "date_desc";

    const DEFUALT_PAGE = 1;

    const DEFUALT_LIMIT = 5;

    protected $jsconfig = [];

    public function getJsConfig()
    {
        $this->jsconfig['filter_url'] = $this->getUrl('*/*/filter');
        $this->jsconfig['reset_url'] = $this->getUrl('*/*/index');
        $this->jsconfig['default_view'] = self::DEFUALT_VIEW;
        $this->jsconfig['default_sort'] = self::DEFUALT_SORT;
        $this->jsconfig['default_page'] = self::DEFUALT_PAGE;
        $this->jsconfig['default_limit'] = self::DEFUALT_LIMIT;
        return json_encode($this->jsconfig);
    }

    public function getSortByOptions()
    {
        return [
            'date_desc' => __('Date - Descending'),
            'date_asc' => __('Date - Ascending'),
            'quote_desc' => __('Ref.Id - Descending'),
            'quote_asc' => __('Ref.Id - Ascending'),
            'price_desc' => __('Price - Descending'),
            'price_asc' => __('Price - Ascending'),
            'jewellery_desc' => __('Quoted Items - Descending'),
            'jewellery_asc' => __('Quoted Items - Ascending'),
        ];
    }
}
