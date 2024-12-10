<?php

namespace Gemfind\Ringbuilder\Block\Diamond;

use Magento\Framework\View\Element\Template\Context;

use Gemfind\Ringbuilder\Helper\Data as Helper;

use Magento\Framework\Session\SessionManagerInterface;

use Magento\Framework\View\Element\Template;

use Magento\Store\Model\StoreManagerInterface;



class Index extends Template

{
    const DIAMOND_COOKIE_NAME = 'diamondsetting';

    const COOKIE_NAME_RING = 'ringsetting';
    /**

     * @var string

     */

    protected $_template = 'Gemfind_Ringbuilder::diamond/index.phtml';



    /**

     * @var Helper

     */

    protected $helper;



    /**

     * @var SessionManagerInterface

     */

    protected $sessionManager;



    /**

     * Store manager

     *

     * @var \Magento\Store\Model\StoreManagerInterface

     */

    protected $_storeManager;



    /**

     * Currency Interface

     *

     * @var \Magento\Framework\Locale\CurrencyInterface

     */

    protected $_localeCurrency;


    protected $_cookieManager;


    protected $_cookieMetadataFactory;



    /**

     * Index constructor.

     * @param Context $context

     * @param Helper $helper

     * @param SessionManagerInterface $sessionManager

     * @param StoreManagerInterface $storeManager

     * @param CurrencyInterface $localeCurrency

     * @param array $data

     */

    public function __construct(

        Context $context,

        Helper $helper,

        SessionManagerInterface $sessionManager,

        StoreManagerInterface $storeManager,

        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,

        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,

        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,

        array $data = []

    ) {



        parent::__construct($context, $data);

        $this->helper = $helper;

        $this->sessionManager = $sessionManager;

        $this->_storeManager = $storeManager;

        $this->_localeCurrency = $localeCurrency;

        $this->_cookieManager = $cookieManager;

        $this->_cookieMetadataFactory = $cookieMetadataFactory;

    }



    /**

     * @return $this

     * @throws \Magento\Framework\Exception\LocalizedException

     * @throws \Magento\Framework\Exception\NoSuchEntityException

     */

    public function _prepareLayout()

    {

        return parent::_prepareLayout();

    }



    /**

     * @return bool

     */

    public function isGemfindEnable()

    {

        return $this->helper->isGemfindEnabled();

    }



    /**

     * @return bool

     */

    public function isFancyDiamondEnabled()

    {

        return $this->helper->isFancyDiamondEnabled();

    }



    /**

     * @return string

     */

    public function getFormAction()

    {

        if ($this->isGemfindEnable()) {

            return $this->getUrl('ringbuilder/diamond/diamondsearch', ['_secure' => true]);

        }

        return $this->getUrl('ringbuilder/diamond/index', ['_secure' => true]);

    }



    /**

     * @return mixed

     */

    public function getResultsPerPage()

    {

        return $this->helper->getResultPerPage();

    }





    /**

     * @return mixed

     */

    public function getStyleSettings()

    {

        return $this->helper->getStyleSetting();

    }





    /**

     * @return mixed

     */

    public function getAdditionalCss()

    {

        return $this->helper->getAdditionalCss();

    }





    /**

     * @return SessionManagerInterface

     */

    public function getSession()

    {

        return $this->sessionManager;

    }



    /**

     * @return Query string parameters

     */

    public function getPara()

    {

        $request = $this->getRequest()->getParams();

        if(isset($request['type'])){

            return $request['type'];

        }

    }



    /**

     * @return Currency Symbol

     */

    public function getCurrencySymbol() {

        $code = $this->_storeManager->getStore()->getCurrentCurrencyCode();

        if($code == 'USD'){

            return 'US'.$this->_localeCurrency->getCurrency($code)->getSymbol();

        }

        return $this->_localeCurrency->getCurrency($code)->getSymbol();

    }





    /**

     * @return array

     */

    public function getActiveNavigation(){

        $navigationcollection = $this->helper->getActiveNavigation();

        if($navigationcollection['total'] > 0){

            return $navigationcollection['navigation'];

        } else {

            return;

        }

    }

    /**

     * @return string

     */

    public function checkRingCookie(){

        $metadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()->setPath('/');

        $this->_cookieManager->deleteCookie(self::DIAMOND_COOKIE_NAME, $metadata);

        $diamondcookie = $this->_cookieManager->getCookie(self::COOKIE_NAME_RING);

        if($diamondcookie){

            return true;

        } else {

            return false;

        }

    }


    /**
     * @return array
     */
    public function getShapedefaultfilter(){
        $request = $this->getRequest()->getParams();
        // WD-4029 Set default diamond shape as round
        return isset($request['defaultshapevalue']) ? $request['defaultshapevalue'] : 'round';
    }

    /**
     * @return mixed
    */
    public function getRingCookieData(){
       $ringsettingcookie = json_decode($this->_cookieManager->getCookie('saveringbackvalue'));
       return $ringsettingcookie;
    }
	public function diamondReport(){
		 return $this->helper->getJCOptiondata();
    }

	public function getDiamondViewUrl($urlstring) {
        return $this->_urlBuilder->getUrl('ringbuilder/diamond/view', ['path' => $urlstring, '_secure' => true]);
    }

    /**
     * @return mixed
    */

    public function getSaveFilterCookieData(){

        if($this->getFiltermode() == 'navfancycolored'){
            return $this->_cookieManager->getCookie('savefiltercookiefancy');
        } else if($this->getFiltermode() == 'navlabgrown'){
            return $this->_cookieManager->getCookie('savefiltercookielabgrown');
        } else if($this->getFiltermode() == 'navstandard'){
            return $this->_cookieManager->getCookie('savefiltercookie');
        } else {
            return;
        }

    }


    /**
     * @return mixed
    */
    public function getSaveBackValueCookieData(){
       if($this->getFiltermode() == 'navfancycolored'){
            return $this->_cookieManager->getCookie('savebackvaluediafancy');
        } else if($this->getFiltermode() == 'navlabgrown'){
            return $this->_cookieManager->getCookie('savebackvaluedialabgrown');
        } else if($this->getFiltermode() == 'navstandard'){
            return $this->_cookieManager->getCookie('savebackvaluedia');
        } else {
            return;
        }
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        if($this->helper->getDefaultView()){
            return $this->helper->getDefaultView();
        } else {
            return 'grid';
        }
    }

    public function getSaveViewType() {
        return $this->_cookieManager->getCookie('saveViewType');
    }
}



