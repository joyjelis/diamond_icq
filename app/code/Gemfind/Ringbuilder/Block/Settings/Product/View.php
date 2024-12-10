<?php



namespace Gemfind\Ringbuilder\Block\Settings\Product;



use Magento\Framework\View\Element\Template\Context;

use Magento\Framework\View\Element\Template;

use Gemfind\Ringbuilder\Helper\Data as Helper;

use Magento\Catalog\Helper\Image as ImageHelper;

use Magento\Store\Model\StoreManagerInterface;



class View extends Template

{

    const DIAMOND_COOKIE_NAME = 'diamondsetting';
    const RING_COOKIE_NAME = 'ringsetting';


    /**

     * @var string

     */

    protected $_template = 'settings/product/view.phtml';



    /**

     * @var Helper

     */

    protected $helper;



    /**

     * @var product data

     */

    protected $product;



    /**

     * @var ImageHelper

     */

    protected $imageHelper;



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



    /**

     * Page Config

     *

     * @var \Magento\Framework\View\Page\Config

     */

    protected $_pageConfig;



    /**

     * View constructor.

     * @param Context $context

     * @param Helper $helper

     * @param ImageHelper $imageHelper

     * @param StoreManagerInterface $storeManager

     * @param CurrencyInterface $localeCurrency

     * @param Config $pageConfig

     * @param CookieManagerInterface $cookieManager

     * @param CookieMetadataFactory $cookieMetadataFactory

     * @param array $data

     */

    public function __construct(

        Context $context,

        Helper $helper,

        ImageHelper $imageHelper,

        StoreManagerInterface $storeManager,

        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,

        \Magento\Framework\View\Page\Config $pageConfig,

        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,

        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,

        array $data = []

    ) {

    

        $this->helper = $helper;

        $this->imageHelper = $imageHelper;

        $this->_storeManager = $storeManager;

        $this->_localeCurrency = $localeCurrency;

        $this->_pageConfig = $pageConfig;

        $this->_cookieManager = $cookieManager;

        $this->_cookieMetadataFactory = $cookieMetadataFactory;

        parent::__construct($context);

    }



    /**

     * @return $this

     * @throws \Magento\Framework\Exception\LocalizedException

     * @throws \Magento\Framework\Exception\NoSuchEntityException

     */

    public function _prepareLayout()

    {

        $this->_pageConfig->getTitle()->set($this->getProductTitle());
     
        $this->_pageConfig->setDescription($this->Metadescription());
        
        return parent::_prepareLayout();

    }



    /**

     * @return string

     */

    public function getSubmitUrl($settingid)

    {

        return $this->getUrl('ringbuilder/settings/add', ['id'=>$settingid,'_secure' => true]);

    }



    



    /**

     * @return string

     */

    public function getAddDiamondUrl($settingid)

    {

        return $this->getUrl('ringbuilder/settings/adddiamond', ['id'=>$settingid,'_secure' => true]);

    }





    /**

     * @return array|product

     */

    public function getProduct()

    {

        $id = $this->excludeid();

        if (!$this->product) {

                $this->product = (array)$this->helper->getRingById($id);      
                $this->triggerClick($this->product);
        }

        return $this->product;

    }



    /**

     * @return array|product

     */

    public function getProductTitle()

    {

        $product = $this->getProduct();

        if(sizeof($product['ringData']) > 0) { 

            return $product['ringData']['settingName'];

        } else {

            return 'Setting Detail';    

        }

        

    }

    /**

     * @return string

    */

    public function Metadescription(){
        
        $product = $this->getProduct();
        if(sizeof($product['ringData']) > 0) { 
            if($product['ringData']['metalType']){
                $metaltype = $product['ringData']['metalType']. ' Metal Type ';
            } else {
                $metaltype = '';
            }
            return $metaltype.$product['ringData']['settingName'];
        } else {
            return 'Setting Meta Description';    
        }

    }

    /**

     * @return string

    */


    public function excludeid(){
    
        $urlstring = $this->getRequest()->getParam('path');     
    
        $urlarray = explode('-sku-', $urlstring);
    
        return $urlarray[1];

    }



    

    /**

     * @return mixed

    */

    public function isHintEnabled() {

        return $this->helper->isHintEnabled();

    }



    /**

     * @return mixed

     */

    public function isEmailtoFriendEnabled() {

        return $this->helper->isEmailtoFriendEnabled();

    }

    /**

     * @return mixed

    */

    public function isMoreInfoEnabled() {

        return $this->helper->isMoreInfoEnabled();

    }



    /**

     * @return mixed

     */

    public function isScheduleViewingEnabled() {

        return $this->helper->isScheduleViewingEnabled();

    }

    /**

     * @return mixed

     */

    public function isPrintDetailEnabled() {

        return $this->helper->getPrintDiamond();

    }



    /**

     * @return mixed

     */

    public function getResultsPerPage()

    {

        return $this->helper->getResultPerPage();

    }



    /**

     * @return string

     */

    public function getFormAction() {

        return $this->getUrl('ringbuilder/settings/resultdrophint', ['_secure' => true]);

    }



    /**

     * @return string

     */

    public function getEmailFrndFormAction() {

        return $this->getUrl('ringbuilder/settings/resultemailfriend', ['_secure' => true]);

    }



    /**

     * @return string

     */

    public function getReqInfoFormAction() {

        return $this->getUrl('ringbuilder/settings/resultreqinfo', ['_secure' => true]);

    }



    /**

     * @return string

     */

    public function getScheViewFormAction() {

        return $this->getUrl('ringbuilder/settings/resultscheview', ['_secure' => true]);

    }



    /**

     * @return string

     */

    public function getSearchFormAction()

    {

        return $this->getUrl('ringbuilder/settings/diamondsearch', ['_secure' => true]);

    }



    /**

     * @return string

    */

    public function getUrl($route = '', $params = [])

    {

        return $this->_urlBuilder->getUrl($route, $params);

    }

    /**

     * @return string

    */

    public function is_404($url) {

        $handle = curl_init($url);

        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($handle, CURLOPT_TIMEOUT, $this->helper->getApiTimeout());

        /* Get the HTML or whatever is linked in $url. */

        $response = curl_exec($handle);

        /* Check for 404 (file not found). */

        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);

        /* If the document has loaded successfully without any redirection or error */

        if ($httpCode >= 200 && $httpCode < 300) {

            return false;

        } else {

            return true;

        }

    }

    /**

     * @return string

     */

    public function checkDiamondCookie(){

        $diamondcookie = $this->_cookieManager->getCookie(self::DIAMOND_COOKIE_NAME);

        if($diamondcookie){

            return true;

        } else {

            return false;            

        }

    }

    /**
     * @return mixed
    */
    public function getDiamondCookieData(){
       $diamondsettingcookie = json_decode($this->_cookieManager->getCookie('diamondsetting'));     
       return $diamondsettingcookie;     
    }

    /**
     * @return mixed
    */
    public function getRingCookieData(){
       $ringsettingcookie = json_decode($this->_cookieManager->getCookie('saveringbackvalue'));     
       return $ringsettingcookie;     
    }

    /**
     * @return mixed
    */
    public function getIslabsetting(){
       return $this->_cookieManager->getCookie('islabsettings');   
    }

    /**
     * @return mixed
    */
    public function getMetaltype($metaltype,$data){
       foreach ($data as $value) {
                $value = (array) $value;
                $dataarraymetaltype[$value['centerStoneSize']][$value['metalType']][] = array('gfInventoryId' => $value['gfInventoryId'], 'centerStoneSize' => $value['centerStoneSize'],);
            } 
        ksort($dataarraymetaltype);
        foreach ($dataarraymetaltype as $finkey =>$fiinavalue) {
            foreach ($fiinavalue as $key => $value) {
                $finalmetaldata[$key][] = array('center' => $finkey, 'gfid' => $value[0]['gfInventoryId']);
            }
          }
        foreach ($finalmetaldata as $finalkey => $finalvalue) {
              $finaldata[] = array('metaltype' => $finalkey, 'gfid' => $finalvalue[0]['gfid']);
          }  
        return $finaldata; 
    }



    /**
     * @return mixed
    */
    public function getSidestone($metaltype,$data){
       foreach ($data as $value) {
                $value = (array) $value;
                $dataarraywithoutsidestone[$value['metalType']][$value['sideStoneQuality']][] = array('gfInventoryId' => $value['gfInventoryId'], 'sideStoneQuality' => $value['sideStoneQuality'], 'centerStoneSize' => $value['centerStoneSize'],);
            }   
        return $dataarraywithoutsidestone[$metaltype];  
    }

    /**
     * @return mixed
    */
    public function getSidestonefinal($sidestone,$data)
    {   
        $keys = array_column($data[$sidestone], 'centerStoneSize');

        array_multisort($keys, SORT_ASC, $data[$sidestone]);

        return array('gfInventoryId' => $data[$sidestone][0]['gfInventoryId'], 'sideStoneQuality' => $data[$sidestone][0]['sideStoneQuality'] );  
    }

    /**
     * @return mixed
    */
    public function getCenterstone($metaltype,$sidestone,$data){
        $dataarraywithoutsidestone = array();
        if($sidestone == null){
            foreach ($data as $value) {
                $value = (array) $value;
                $dataarraywithoutsidestone[$value['metalType']][] = array('gfInventoryId' => $value['gfInventoryId'], 'centerStoneSize' => $value['centerStoneSize'],);
            } 
            usort($dataarraywithoutsidestone[$metaltype], function($a, $b) {
                  return $a['centerStoneSize'] <=> $b['centerStoneSize'];
              }); 
            return $dataarraywithoutsidestone[$metaltype];
        } else {
            foreach ($data as $value) {
                $value = (array) $value;
                $dataarraywithoutsidestone[$value['metalType']][$value['sideStoneQuality']][] = array('gfInventoryId' => $value['gfInventoryId'], 'centerStoneSize' => $value['centerStoneSize'],);
            }
            usort($dataarraywithoutsidestone[$metaltype][$sidestone[0]], function($a, $b) {
                  return $a['centerStoneSize'] <=> $b['centerStoneSize'];
              }); 
            return $dataarraywithoutsidestone[$metaltype][$sidestone[0]];               
        }
    }    

    /**
     * @return null
     */
    public function triggerClick($ringdata)
    { 
        $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
        $RetailerID = $VendorID = $GFInventoryID= $URL= $StyleNumber= $DealerStockNumber= $RetailerStockNumber= $Price= ''; 
        $VendorID = 'VendorID='.($ringdata['ringData']['vendorId'])?$ringdata['ringData']['vendorId'].'&':'&';
        $GFInventoryID = 'GFInventoryID=&';
        $URL = 'URL='.$urlInterface->getCurrentUrl().'&';
        $settingId = 'SettingID='.$ringdata['ringData']['settingId'].'&';
        $RetailerStockNumber = ($ringdata['ringData']['retailerInfo']->retailerStockNo)?$ringdata['ringData']['retailerInfo']->retailerStockNo.'&':'&';
        $caratWeight = ($ringdata['ringData']['centerStoneMaxCarat'])?$ringdata['ringData']['centerStoneMaxCarat'].'&':'&';
        $Price = ($ringdata['ringData']['cost'])?$ringdata['ringData']['cost']:'';
        $posturl = str_replace(' ', '+', 'https://platform.jewelcloud.com/ProductTracking.aspx?'.$RetailerID.$VendorID.$GFInventoryID.$URL.$settingId.'RetailerStockNumber='.$RetailerStockNumber.'Carat='.$caratWeight.'Price='.$Price);
        
    }


    /**
     * @return bool
     */
    public function getGemfindEnabledPoweredBy()
    {   
        return $this->helper->isGemfindEnabledPoweredBy();
    }

    public function diamondReport(){
		 return $this->helper->getJCOptiondata();
    }

    /**
     * @return bool
     */
    public function getGemfindEnabledTryon()
    {   
        return $this->helper->isGemfindEnabledTryon();
    }

    /** 
     * @return string
    */

    public function isCaptchakey() {

        return $this->helper->getCaptchaKey();

    }
}

