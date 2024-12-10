<?php







namespace Gemfind\Ringbuilder\Block\Diamond\Product;







use Magento\Framework\View\Element\Template\Context;



use Magento\Framework\View\Element\Template;



use Gemfind\Ringbuilder\Helper\Data as Helper;



use Magento\Catalog\Helper\Image as ImageHelper;



use Magento\Store\Model\StoreManagerInterface;







class View extends Template



{





    const COOKIE_NAME_RING = 'ringsetting';



    /**



     * @var string



     */



    protected $_template = 'diamond/view.phtml';







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



        // add Home breadcrumb

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {

        

            $breadcrumbs->addCrumb(

        

                'search',

        

                [

        

                    'label' => __('Return To Search Results'),

        

                    'title' => __('Return To Search Results'),

        

                    'link' => $this->getFilterMode()

        

                ]

        

            );

        

        }



        

        $this->_pageConfig->getTitle()->set($this->getProductTitle());

         

        $this->_pageConfig->setDescription($this->Metadescription());

        

        return parent::_prepareLayout();



    }







    /**



     * @return string



     */



    public function getSubmitUrl($diamondid)



    {



        return $this->getUrl('ringbuilder/diamond/add', ['id'=>$diamondid,'_secure' => true]);



    }







    /**



     * @return string



     */



    public function getAddDiamondUrl($diamondid)



    {



        return $this->getUrl('ringbuilder/diamond/addring', ['id'=>$diamondid,'_secure' => true]);



    }







    /**



     * @return array|product



     */



    public function getProduct()



    {



        $id = $this->excludeid();



        $type = $this->getRequest()->getParam('type');







        if (!$this->product) {



            if($type == 'labcreated'){
                $this->product = (array)$this->helper->getDiamondByIdtype($id,$type);
                $this->product['diamondData']['type'] = 'labcreated';
            } else if($type == 'fancy') {
                $this->product = (array)$this->helper->getDiamondByIdtype($id,$type);
                $this->product['diamondData']['type'] = 'fancy';
            } else {
                $this->product = (array)$this->helper->getDiamondById($id);    
            }



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



        if(sizeof($product['diamondData']) > 0) { 



            return $product['diamondData']['mainHeader'];



        } else {



            return 'Diamond Detail';    



        }



        



    }







    public function getFilterMode(){



        $product = $this->getProduct();



        $type = $this->getRequest()->getParam('type');



        if(sizeof($product['diamondData']) > 0) { 



            if($product['diamondData']['fancyColorMainBody']){



                return $this->_storeManager->getStore()->getBaseUrl().'ringbuilder/diamond/type/navfancycolored';



            } else if($type == 'labcreated') {



                return $this->_storeManager->getStore()->getBaseUrl().'ringbuilder/diamond/type/navlabgrown';



            } else {



                return $this->_storeManager->getStore()->getBaseUrl().'ringbuilder/diamond';



            }



            



        } else {



                return $this->_storeManager->getStore()->getBaseUrl().'ringbuilder/diamond';



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



     * @return string



     */



    public function getPlaceholderUrl()



    {



        return $this->imageHelper->getDefaultPlaceholderUrl('image');



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



        return $this->getUrl('ringbuilder/diamond/resultdrophint', ['_secure' => true]);



    }







    /**



     * @return string



     */



    public function getEmailFrndFormAction() {



        return $this->getUrl('ringbuilder/diamond/resultemailfriend', ['_secure' => true]);



    }







    /**



     * @return string



     */



    public function getReqInfoFormAction() {



        return $this->getUrl('ringbuilder/diamond/resultreqinfo', ['_secure' => true]);



    }







    /**



     * @return string



     */



    public function getScheViewFormAction() {



        return $this->getUrl('ringbuilder/diamond/resultscheview', ['_secure' => true]);



    }







    /**



     * @return string



     */



    public function getSearchFormAction()



    {



        return $this->getUrl('ringbuilder/diamond/diamondsearch', ['_secure' => true]);



    }







    /**



     * @return string



     */



    public function getUrl($route = '', $params = []) {



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



    public function checkRingCookie(){



        $diamondcookie = $this->_cookieManager->getCookie(self::COOKIE_NAME_RING);



        if($diamondcookie){



            return true;



        } else {



            return false;            



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



     * @return string



     */



    public function Metadescription(){

        

        $product = $this->getProduct();

        

        if(sizeof($product['diamondData']) > 0) { 

        

            if(isset($product['diamondData']['shape'])){

        

                $shape = $product['diamondData']['shape'].' Shape, ';

        

            }else{

        

                $shape = '';            

        

            }



            if(isset($product['diamondData']['cut'])){

        

                $cut = $product['diamondData']['cut'].' Cut, ';

        

            }else{

        

                $cut = '';          

        

            }



            if(isset($product['diamondData']['color'])){

        

                $color = $product['diamondData']['color'].' Color, ';

        

            }else{

        

                $color = '';            

        

            }





            if(isset($product['diamondData']['clarity'])){



                $clarity = $product['diamondData']['clarity'].' Clarity, ';



            }else{



                $clarity = '';          



            }





            if(isset($product['diamondData']['carat'])){



                $carat = $product['diamondData']['carat'].' Carat, ';



            }else{



                $carat = '';            



            }





            if(isset($product['diamondData']['polish'])){



                $polish = $product['diamondData']['polish'].' Polish, ';



            }else{



                $polish = '';           



            }







            if(isset($product['diamondData']['symmetry'])){



                $symmetry = $product['diamondData']['symmetry'].' Symmetry, ';



            }else{



                $symmetry = '';         



            }



            return 'This '.$shape.$cut.$color.$clarity.$carat.$polish.$symmetry.'Diamond comes accompanied by a diamond grading 

            report from '.$product['diamondData']['certificate'];

        }



    }



    /**



     * @return string



     */



    public function Metatags(){

        

        $product = $this->getProduct();

        

        if(sizeof($product['diamondData']) > 0) { 

        

            return $product['diamondData']['mainHeader'];

        

        }

    

    }



    /**



     * @return string



     */



    public function getDiamondviewurl($param,$type)

    {

        return $this->getUrl('ringbuilder/diamond/view', ['path' => $param, 'type' => $type, '_secure' => true]);

    }



    

    /**

     * @return mixed

    */

    public function getRingCaratCookieData(){

       $ringsettingcookie = json_decode($this->_cookieManager->getCookie('ringsetting'));     

       return $ringsettingcookie;     

    }



    /**

     * @return null

     */

    public function triggerClick($diamonddata)

    {

        $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');

        if(sizeof($diamonddata['diamondData']) > 0){

        $RetailerID = $VendorID = $GFInventoryID= $URL= $StyleNumber= $DealerStockNumber= $RetailerStockNumber= $Price= ''; 

        $VendorID = 'VendorID='.($diamonddata['diamondData']['vendorID'])?$diamonddata['diamondData']['vendorID'].'&':'&';

        $GFInventoryID = 'GFInventoryID=&';

        $URL = 'URL='.$urlInterface->getCurrentUrl().'&';

        $DiamondId = 'DiamondID='.$diamonddata['diamondData']['diamondId'].'&';

        $DealerStockNumber = ($diamonddata['diamondData']['vendorStockNo'])?$diamonddata['diamondData']['vendorStockNo'].'&':'&';

        $RetailerStockNumber = ($diamonddata['diamondData']['retailerInfo']->retailerStockNo)?$diamonddata['diamondData']['retailerInfo']->retailerStockNo.'&':'&';

        $caratWeight = ($diamonddata['diamondData']['caratWeight'])?$diamonddata['diamondData']['caratWeight'].'&':'&';

        $cut = ($diamonddata['diamondData']['cut'])?$diamonddata['diamondData']['cut'].'&':'&';

        $color = ($diamonddata['diamondData']['color'])?$diamonddata['diamondData']['color'].'&':'&';

        $clarity = ($diamonddata['diamondData']['clarity'])?$diamonddata['diamondData']['clarity'].'&':'&';

        $depth = ($diamonddata['diamondData']['depth'])?$diamonddata['diamondData']['depth'].'&':'&';

        $table = ($diamonddata['diamondData']['table'])?$diamonddata['diamondData']['table'].'&':'&';

        $polish = ($diamonddata['diamondData']['polish'])?$diamonddata['diamondData']['polish'].'&':'&';

        $symmetry = ($diamonddata['diamondData']['symmetry'])?$diamonddata['diamondData']['symmetry'].'&':'&';

        $shape = ($diamonddata['diamondData']['shape'])?$diamonddata['diamondData']['shape'].'&':'&';

        $Price = ($diamonddata['diamondData']['fltPrice'])?$diamonddata['diamondData']['fltPrice']:'';

        $posturl = str_replace(' ', '+', 'https://platform.jewelcloud.com/ProductTracking.aspx?'.$RetailerID.$VendorID.$GFInventoryID.$URL.$DiamondId.'DealerStockNumber='.$DealerStockNumber.'RetailerStockNumber='.$RetailerStockNumber.'Carat='.$caratWeight.'Cut='.$cut.'Color='.$color.'Clarity='.$clarity.'Ddepth='.$depth.'Table='.$table.'Polish='.$polish.'Symmetry='.$symmetry.'Shape='.$shape.'Price='.$Price);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $posturl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->helper->getApiTimeout());

        $responce = curl_exec($curl);

        $results = json_decode($responce);

        if (curl_errno($curl)) {

        }

        curl_close($curl);

        }

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
	public function getDiamondUrl($urlstring) {
        return $this->_urlBuilder->getUrl('ringbuilder/diamond/view', ['path' => $urlstring, '_secure' => true]);
    }

    /** 
     * @return string
    */

    public function isCaptchakey() {

        return $this->helper->getCaptchaKey();

    }

}



