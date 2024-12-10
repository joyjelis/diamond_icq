<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME Calalog
 * @author    FME extensions <support@fmeextensions.com
>
 * @package   FME_ProductSorting
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/
)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ProductSorting\Plugin;

use FME\ProductSorting\Model\System\SortOptions;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use FME\ProductSorting\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;

class SortOption extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_dataHelper;

    /**
     * StoreManager
     *
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * SortType
     *
     * @var array
     */
    public $SortType;
    protected $request;

    /**
     * AddSortOption constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param SortType              $SortType
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        SortOptions $SortType,
        Data $dataHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->storeManager = $storeManager;
        $this->SortType = $SortType->toOptionArray();
        parent::__construct($context);
        $this->request = $request;
        $this->_dataHelper = $dataHelper;
    }
    
    public function getCustomSortingOptions()
    {
        $sortOptionString = $this->_dataHelper->getGeneralConfig('sort_order');
        $sortOptionArray = explode(",", $sortOptionString);
        $customOrder=null;
         
        foreach ($sortOptionArray as $val) {
            if ($val=="Best Seller") {
                $customOrder['best_seller'] = __('Best Seller');
            }

            if ($val=="New Arrival") {
                $customOrder['created_at'] = __('New Arrival');
            }

            if ($val=="Stock Quantity") {
                $customOrder['stockquantity'] = __('Stock Quantity');
            }

            if ($val=="Top Rated") {
                $customOrder['top_rated'] = __('Top Rated');
            }

            if ($val=="Biggest Saving") {
                $customOrder['saving'] = __('Biggest Saving');
            }

            if ($val=="Price") {
                $customOrder['price'] = __('Price');
            }

            if ($val=="Most Viewed") {
                $customOrder['most_viewed'] = __('Most Viewed');
            }

            if ($val=="Reviews Count") {
                $customOrder['review_count'] = __('Reviews Count');
            }

            if ($val=="Now in Wishlists") {
                $customOrder['wished'] = __('Now in Wishlists');
            }

            if ($val=="Product Name") {
                $customOrder['name'] = __('Product Name');
            }

            if ($val=="Position") {
                $customOrder['position'] = __('Position');
            }

            if ($val=="Price: low to high") {
                $customOrder['price_asc'] = __('Price: low to high');
            }

            if ($val=="Price: high to low") {
                $customOrder['price_desc'] = __('Price: high to low');
            }
        }

        return $customOrder; //check its null or not
    }

    /**
     * Add sort order option created_at to frontend
     *
     * @param \Magento\Catalog\Model\Config $configmodel
     * @param array                         $options
     *
     * @return mixed
     */
    public function afterGetAttributeUsedForSortByArray($configmodel, $options)
    {

        $moduleName = $this->request->getModuleName();
        $controller = $this->request->getControllerName();
        $action     = $this->request->getActionName();
        $route      = $this->request->getRouteName();

        $isEnabled = $this->scopeConfig->getValue(
            'productSorting/general/enabled',
            ScopeInterface::SCOPE_STORE,
            null
        );

        if ($isEnabled) {
            $isLogIN = $this->_dataHelper->getCustomerId();
            $customOrderSort = $this->getCustomSortingOptions();
            $CategorySortOrder = null;
            unset($options['imageProduct']);
            unset($options['outOfStock']);
            unset($options['statusOrder']);
            unset($options['status_order']);
            if (!empty($customOrderSort)) {
                if ($controller=='category') {
                    unset($options['position']);
                    unset($options['name']);
                    unset($options['price']);
                    unset($options['featured']);
                    unset($options['created_at']);
                    unset($options['bestseller_order']);

                    $SortByList = $this->scopeConfig->getValue(
                        'productSorting/general/sortOption',
                        ScopeInterface::SCOPE_STORE,
                        null
                    );

                    foreach ($this->SortType as $optionlist) {
                        if (isset($options[$optionlist['value']])) {
                            unset($options[$optionlist['value']]);
                        }
                    }

         
                    foreach ($customOrderSort as $key => $value) {
                        $enable = $key . '/enable';
                        $applyPage = $key . '/applyPage';
                        $label = $key . '/label';
                        if ($this->_dataHelper->getConfig($enable) == 1) {
                            $page = $this->_dataHelper->getConfig($applyPage);
                            if ($key == 'wished') {
                                if ($isLogIN) {
                                    if ($page != null) {
                                        $customLabel = $this->_dataHelper->getConfig($label);
                                        if ($page == 2) {
                                            if ($customLabel != null) {
                                                $options[$key] = __($customLabel);
                                            } else {
                                                $options[$key] = __($value);
                                            }
                                        } elseif (strcmp($page, "1,2")==0) {
                                            if ($customLabel != null) {
                                                $options[$key] = __($customLabel);
                                            } else {
                                                $options[$key] = __($value);
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($page != null) {
                                    $customLabel = $this->_dataHelper->getConfig($label);
                                    if ($page == 2) {
                                        if ($customLabel != null) {
                                            $options[$key] = __($customLabel);
                                        } else {
                                            $options[$key] = __($value);
                                        }
                                    } elseif (strcmp($page, "1,2")==0) {
                                        if ($customLabel != null) {
                                            $options[$key] = __($customLabel);
                                        } else {
                                            $options[$key] = __($value);
                                        }
                                    }
                                }
                            }
                        }
                    } //endForeach
                    $sortVal = $this->_dataHelper->getConfig('sorting_default/category_sort');
                    if (empty($options[$sortVal])) {
                        foreach ($customOrderSort as $key => $value) {
                            if ($key ==  $sortVal) {
                                $options[$key] = __($value);
                            }
                        }
                    }
                } //end for controller

                if ($controller=='result') {
                        unset($options['position']);
                        unset($options['name']);
                        unset($options['price']);
                        unset($options['featured']);
                        unset($options['created_at']);
                        unset($options['relevance']);
                        $SortByList = $this->scopeConfig->getValue(
                            'productSorting/general/sortOption',
                            ScopeInterface::SCOPE_STORE,
                            null
                        );

                    foreach ($this->SortType as $optionlist) {
                        if (isset($options[$optionlist['value']])) {
                            unset($options[$optionlist['value']]);
                        }
                    }


                    foreach ($customOrderSort as $key => $value) {
                        $enable = $key . '/enable';
                        $applyPage = $key . '/applyPage';
                        $label = $key . '/label';
                        if ($this->_dataHelper->getConfig($enable) == 1) {
                            $page = $this->_dataHelper->getConfig($applyPage);
                            if ($key == 'wished') {
                                if ($isLogIN) {
                                    if ($page != null) {
                                        $customLabel = $this->_dataHelper->getConfig($label);
                                        if ($page == 2) {
                                            if ($customLabel != null) {
                                                $options[$key] = __($customLabel);
                                            } else {
                                                  $options[$key] = __($value);
                                            }
                                        } elseif (strcmp($page, "1,2")==0) {
                                            if ($customLabel != null) {
                                                $options[$key] = __($customLabel);
                                            } else {
                                                $options[$key] = __($value);
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($page != null) {
                                          $customLabel = $this->_dataHelper->getConfig($label);
                                    if ($page == 2) {
                                        if ($customLabel != null) {
                                            $options[$key] = __($customLabel);
                                        } else {
                                            $options[$key] = __($value);
                                        }
                                    } elseif (strcmp($page, "1,2")==0) {
                                        if ($customLabel != null) {
                                            $options[$key] = __($customLabel);
                                        } else {
                                            $options[$key] = __($value);
                                        }
                                    }
                                }
                            }
                        }

                        if ($this->_dataHelper->getConfig('sorting_default/search_sort') == $key) {
                            $customLabel = $this->_dataHelper->getConfig($label);
                            if ($customLabel != null) {
                                $options[$key] = __($customLabel);
                            } else {
                                $options[$key] = __($value);
                            }
                        }
                    } //endForeac
                            unset($options['relevance']);

                    if ($this->_dataHelper->getConfig('relevance/enable') == 1) {
                           $customLabel = $this->_dataHelper->getConfig('relevance/label');
                        if ($customLabel != null) {
                            $options['relevance'] = __($customLabel);
                        } else {
                            $options['relevance'] = __('Relevanc');
                        }
                    } else {
                        unset($options['relevance']);
                    }

                         $sortVal = $this->_dataHelper->getConfig('sorting_default/search_sort');
            

                    if (empty($options[$sortVal])) {
                        $sortVal = $this->_dataHelper->getConfig('sorting_default/search_sort');
                        foreach ($customOrderSort as $key => $value) {
                            if ($key ==  $sortVal) {
                                $options[$key] = __($value);
                            }
                        }
                    }
                } //ifresultend
            }//if null check function
        } else {
            unset($options['best_seller']);
            unset($options['top_rated']);
            unset($options['created_at']);
            unset($options['most_viewed']);
            unset($options['review_count']);
            unset($options['imageProduct']);
            unset($options['status_order']);
            unset($options['outOfStock']);
            unset($options['statusOrder']);
            unset($options['saving']);
            unset($options['price_asc']);
            unset($options['price_desc']);
            unset($options['stockquantity']);
            unset($options['wished']);
        }

        return $options;
    }
}
