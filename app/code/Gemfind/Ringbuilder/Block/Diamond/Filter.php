<?php

namespace Gemfind\Ringbuilder\Block\Diamond;

use Magento\Framework\View\Element\Template;
use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Filter extends \Magento\Framework\View\Element\Template
{


    /**
     * @var Helper
     */
    protected $_helper;


    /**
     * @var ProductFactory
     */
    protected $_productFactory;


    /**
     * Attribute collection factory
     *
     * @var AttributeCollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * Currency Interface
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * Filter constructor.
     * @param \Magento\Framework\View\Element\Template $context
     * @param \Gemfind\Ringbuilder\Helper\Data $helper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array $data
     */

    public function __construct(
        Template\Context $context,
        Helper $helper,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        AttributeCollectionFactory $attributeCollectionFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_cookieManager = $cookieManager;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getDiamondAttributes()
    {
        $attributeData = [];
        $attrcodearray = ['gemfind_diamond_shape','gemfind_diamond_cut','gemfind_diamond_intintensity'];
        foreach ($attrcodearray as $value) {
            $attributes = $this->getAttributes($value);
            foreach ($attributes as $attribute) {
                $code = $attribute->getAttributeCode();
                $attributeData[$value] = [
                    'options' => $attribute->getSource()->getAllOptions(false),
                ];
            }
        }
        return $attributeData;
    }

    /**
     * @return mixed
     */
    public function getAttributes($code)
    {
            $product = $this->_productFactory->create();
            $attributes = $this->_attributeCollectionFactory
                ->create()
                ->setCodeFilter($code)
                ->addStoreLabel($this->_storeManager->getStore()->getId())
                ->setOrder('main_table.attribute_id', 'asc')
                ->load();
        foreach ($attributes as $attribute) {
            $attribute->setEntity($product->getResource());
        }
        return $attributes;
    }

    /**
     * @return mixed
     */
    public function getDiamondFilters()
    {
        $request = $this->getRequest()->getParams();
        $dealerID = $this->_helper->getUsername();
        
        if (isset($request['filtermode'])) {
            
            if ($request['filtermode'] == 'navstandard') {
                $requestUrl = $this->_helper->getfilterapi().'DealerID='.$dealerID;
            } elseif ($request['filtermode'] == 'navlabgrown') {
                $requestUrl = $this->_helper->getfilterapi().'DealerID='.$dealerID.'&IsLabGrown=true';
            } elseif ($request['filtermode'] == 'navfancycolored') {
                $requestUrl = $this->_helper->getfilterapifancy().'DealerID='.$dealerID;
            } else {
                return;
            }
        } else {
            return;
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->_helper->getApiTimeout());
        $responce = curl_exec($curl);
        $results = (array) json_decode($responce);
        if (sizeof($results) > 1 && $results[0]->message == 'Success') {
            foreach ($results[1] as $value) {
                return $value = (array) $value;
            }
        }
        curl_close($curl);
    }

    /**
     * @return mixed
     */

    public function getActiveFilter()
    {
        $request = $this->getRequest()->getParams();
        if(!array_key_exists('filtermode', $request)){
            $request['filtermode'] = '';
        }
        return $request['filtermode'];
        
    }

    /**
     * @return mixed
     */

    public function getSaveFilterCookieData()
    {
        
        if ($this->getFiltermode() == 'navfancycolored') {
            return $this->_cookieManager->getCookie('savefiltercookiefancy');
        } elseif ($this->getFiltermode() == 'navlabgrown') {
            return $this->_cookieManager->getCookie('savefiltercookielabgrown');
        } elseif ($this->getFiltermode() == 'navstandard') {
            return $this->_cookieManager->getCookie('savefiltercookie');
        } else {
            return;
        }
    }


    /**
     * @return mixed
     */
    public function getSaveBackValueCookieData()
    {
        if ($this->getFiltermode() == 'navfancycolored') {
            return $this->_cookieManager->getCookie('savebackvaluediafancy');
        } elseif ($this->getFiltermode() == 'navlabgrown') {
            return $this->_cookieManager->getCookie('savebackvaluedialabgrown');
        } elseif ($this->getFiltermode() == 'navstandard') {
            return $this->_cookieManager->getCookie('savebackvaluedia');
        } else {
            return;
        }
    }

    /**
     * @return Currency Symbol
     */
    public function getCurrencySymbol()
    {
        $code = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        if ($code == 'USD') {
            return 'US'.$this->_localeCurrency->getCurrency($code)->getSymbol();
        }
        return $this->_localeCurrency->getCurrency($code)->getSymbol();
    }

    /**
     * @return Mode
     */

    public function getFiltermode()
    {
        $request = $this->getRequest()->getParams();
        return (array_key_exists('filtermode', $request) && $request['filtermode']) ? $request['filtermode'] : 'navstandard';
    }

    /**
     * @return mixed
     */
    public function getResultsPerPage()
    {
        return $this->_helper->getResultPerPage();
    }
    
    /**
     * @return string
     */
    public function getDefaultView()
    {
        if ($this->_helper->getDefaultView()) {
            return $this->_helper->getDefaultView();
        } else {
            return 'grid';
        }
    }

    /**
     * @return mixed
     */
    public function getShapedefaultfilter()
    {
        $request = $this->getRequest()->getParams();
        return (array_key_exists('defaultshapevalue', $request) && $request['defaultshapevalue']) ? $request['defaultshapevalue'] : '';
    }

    /**
     * @return mixed
     */
    public function getRingCaratCookieData()
    {
        $ringsettingcookie = json_decode($this->_cookieManager->getCookie('ringsetting'));
        return $ringsettingcookie;
    }

    /**
     * @return mixed
     */
    public function getRingBackCookieData()
    {
        $ringbackcookie = json_decode($this->_cookieManager->getCookie('saveringbackvalue'));
        return $ringbackcookie;
    }

    /**
     * @return mixed
     */
    public function diamondReport()
    {
         return $this->_helper->getJCOptiondata();
    }
}
