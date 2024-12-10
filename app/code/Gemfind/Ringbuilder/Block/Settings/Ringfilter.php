<?php

namespace Gemfind\Ringbuilder\Block\Settings;

use Magento\Framework\View\Element\Template;
use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Ringfilter extends \Magento\Framework\View\Element\Template
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
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array $data
     */

    public function __construct(
        Template\Context $context, 
        Helper $helper,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = array()
    ){
        $this->_productFactory = $productFactory;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_cookieManager = $cookieManager;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $data);
    }


    /**
     * @return mixed
     */
    public function getRingFilters()
    {   

        $request = $this->getRequest()->getParams();
        $dealerID = $this->_helper->getUsername();
        $requestUrl = $this->_helper->getringfilterapi().'DealerID='.$dealerID;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->_helper->getApiTimeout());
        $responce = curl_exec($curl);
        $results = (array) json_decode($responce);
        if(sizeof($results) > 1 && $results[0]->message == 'Success'){
            foreach ($results[1] as $value) {
                return $value = (array) $value;
            }
        }
        curl_close($curl);
    }

    /**
     * @return mixed
     */
    public function getResultsPerPage()
    {
        return $this->_helper->getResultPerPageforRing();
    }

    /**
     * @return mixed
    */
    public function getSaveRingFilterCookieData(){
            return $this->_cookieManager->getCookie('saveringfiltercookie');       
    }

    /**
     * @return mixed
    */
    public function getSaveRingFilterBackCookieData(){
            return $this->_cookieManager->getCookie('saveringbackvalue');       
    }

    /**
     * @return mixed
    */
    public function getDiamondCookieData(){
       $diamondsettingcookie = json_decode($this->_cookieManager->getCookie('diamondsetting'));     
       return $diamondsettingcookie;     
    }

    /**
     * @return string
     */
    public function getRingshapedefaultfilter(){
        $request = $this->getRequest()->getParams();
        return (array_key_exists('defaultringshapevalue', $request) && $request['defaultringshapevalue']) ? $request['defaultringshapevalue'] : '';
    }
}