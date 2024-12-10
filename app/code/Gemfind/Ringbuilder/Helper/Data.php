<?php



namespace Gemfind\Ringbuilder\Helper;

use Magento\Framework\App\Helper\Context;

use Psr\Log\LoggerInterface;

use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_API_TIMEOUT = 10;

    const XML_API_TIMEOUT = 'gemfindringbuilder/general/api_timeout';

    const XML_PATH_GEMFIND_ENABLED = 'gemfindringbuilder/general/enable_in_frontend';

    const XML_PATH_USERNAME = 'gemfindringbuilder/general/username';

    const XML_PATH_ADMINEMAIL = 'gemfindringbuilder/general/adminemail';

    const XML_PATH_ADMINDEALERNAME = 'gemfindringbuilder/general/admindealername';

    const XML_PATH_GEMFIND_ENABLED_POWERED_BY = 'gemfindringbuilder/general/enable_powered_by';

    const XML_PATH_GEMFIND_ENABLED_STICKY_HEADER = 'gemfindringbuilder/general/enable_sticky_header';

    const XML_PATH_GEMFIND_ENABLED_TRYON = 'gemfindringbuilder/general/enable_tryon';

    const XML_PATH_DEALERAUTHAPI = 'gemfindringbuilder/general/dealerauthapi';

    const XML_PATH_GETRINGFILTERAPI = 'gemfindringbuilder/general/getringfilterapi';

    const XML_PATH_GETMOUNTINGLISTAPI = 'gemfindringbuilder/general/getmountinglistapi';

    const XML_PATH_GETMOUNTINGDETAILAPI = 'gemfindringbuilder/general/getmountingdetailapi';

    const XML_PATH_GETRINGSTYLE = 'gemfindringbuilder/general/getringstylesettingapi';

    const XML_PATH_GETNAVIGATIONAPI = 'gemfindringbuilder/general/getnavigationapi';

    const XML_PATH_GETFILTERAPI = 'gemfindringbuilder/general/getfilterapi';

    const XML_PATH_GETFILTERAPIFANCY = 'gemfindringbuilder/general/getfilterapifancy';

    const XML_PATH_GETDIAMONDLISTAPI = 'gemfindringbuilder/general/getdiamondlistapi';

    const XML_PATH_GETJCOPTIONAPI = 'gemfindringbuilder/general/jsoptionsapi';

    const XML_PATH_GETDIAMONDLISTAPIFANCY = 'gemfindringbuilder/general/getdiamondlistapifancy';

    const XML_PATH_GETDIAMONDSHAPEAPI = 'gemfindringbuilder/general/getdiamondshapeapi';

    const XML_PATH_GETDIAMONDDETAILAPI = 'gemfindringbuilder/general/getdiamonddetailapi';

    const XML_PATH_GETSTYLESETTINGAPI = 'gemfindringbuilder/general/getstylesettingapi';

    const XML_PATH_GET_HINT = 'gemfindringbuilder/general/enable_hint';

    const XML_PATH_EMAIL_TO_FRIEND = 'gemfindringbuilder/general/enable_email_friend';

    const XML_PATH_SCHEDULE_VIEWING = 'gemfindringbuilder/general/enable_schedule_viewing';

    const XML_PATH_MORE_INFO = 'gemfindringbuilder/general/enable_more_info';

    const XML_PATH_PRINT = 'gemfindringbuilder/general/enable_print';

    const XML_PATH_DEFAULT_VIEW = 'gemfindringbuilder/general/default_view';

    const XML_PATH_CARAT_RANGE = 'gemfindringbuilder/general/carat_range';

    const XML_PATH_ADDITIONAL_CSS = 'gemfindringbuilder/design/additionl_css';

    const XML_PATH_EMAIL_SENDER = 'gemfindringbuilder/email/identity';

    const XML_PATH_CSV_PATH = 'gemfindringbuilder/feed/path';

    const XML_PATH_GOOGLE_CAPTCHA = 'gemfindringbuilder/general/captcha';
    /**

     * @var LoggerInterface

     */

    protected $logger;



    /**

     * @var \Magento\Store\Model\StoreManagerInterface

     */

    public $_storeManager;


    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**

     * Data constructor.

     * @param Context $context

     * @param LoggerInterface $logger

     * @param \Magento\Store\Model\StoreManagerInterface $storeManager

     */
    
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository
    ) {

        $this->logger = $logger;

        $this->_storeManager=$storeManager;
        $this->_cookieManager = $cookieManager;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->product = $product;
        $this->attributeSetRepository = $attributeSetRepository;

        parent::__construct($context);
    }



    public function getSiteUrl()
    {

        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    /**

     * @return bool

     */

    public function isGemfindEnabled()
    {

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GEMFIND_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getUsername()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_USERNAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getAdminEmail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADMINEMAIL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getDealerName()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADMINDEALERNAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


     /**
      * @return bool
      */
    public function isGemfindEnabledPoweredBy()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GEMFIND_ENABLED_POWERED_BY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * @return bool
     */
    public function isGemfindEnabledStickyHeaderRB()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GEMFIND_ENABLED_STICKY_HEADER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isGemfindEnabledTryon()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GEMFIND_ENABLED_TRYON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**

     * @return mixed

     */

    public function getApiTimeout()
    {

        $api_timeout = $this->scopeConfig->getValue(
            self::XML_API_TIMEOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return (int)$api_timeout;
    }

    /**

     * @return mixed

     */

    public function getDealerAuthapi()
    {

        $url = $this->scopeConfig->getValue(
            self::XML_PATH_DEALERAUTHAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $url;
    }



    /**

     * @return mixed

     */

    public function getringfilterapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETRINGFILTERAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }





    /**

     * @return mixed

     */

    public function getmountinglistapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETMOUNTINGLISTAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }





    /**

     * @return mixed

     */

    public function getmountingdetailapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETMOUNTINGDETAILAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getringstyleapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETRINGSTYLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }







    /**

     * @return mixed

     */

    public function getfilterapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETFILTERAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getfilterapifancy()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETFILTERAPIFANCY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getdiamondlistapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETDIAMONDLISTAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * @return mixed
     */
    public function getJcOptionsapi()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GETJCOPTIONAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getdiamondlistapifancy()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETDIAMONDLISTAPIFANCY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getdiamondshapeapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETDIAMONDSHAPEAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getdiamonddetailapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETDIAMONDDETAILAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getnavigationapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETNAVIGATIONAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getStyleSettingapi()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GETSTYLESETTINGAPI,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function getResultPerPage()
    {

        return 20;
    }


    /**

     * @return mixed

     */

    public function getResultPerPageforRing()
    {

        return 12;
    }



    /**

     * @return mixed

     */

    public function getAdditionalCss()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_ADDITIONAL_CSS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }





    /**

     * @return mixed

     */

    public function isHintEnabled()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_GET_HINT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function isEmailtoFriendEnabled()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TO_FRIEND,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function isScheduleViewingEnabled()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_SCHEDULE_VIEWING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**

     * @return mixed

     */

    public function isMoreInfoEnabled()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_MORE_INFO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }





    /**

     * @return mixed

     */

    public function getPrintDiamond()
    {

        return $this->scopeConfig->getValue(
            self::XML_PATH_PRINT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_VIEW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCaptchaKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLE_CAPTCHA,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



     /**

      * @return email sender

      */

    public function getEmailSender()
    {

        $sender = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($sender==''):

            $sender = $this->senderEmail();

        endif;

        

        return $sender;
    }



    

    public function senderEmail($type = null, $storeId = null)
    {

        $sender ['email'] = $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $sender['name'] = $this->scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);



        return $sender;
    }

    

    public function getEmailTemplate($templateId)
    {

        return $this->scopeConfig->getValue($templateId, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getDiamondCookieData()
    {
        $diamondsettingcookie = json_decode($this->_cookieManager->getCookie('diamondsetting'));
        return $diamondsettingcookie;
    }

    /**

     * @param $requestParam

     * @return array

     */

    public function sendRingRequest($requestParam)
    {



        $Shape = $MetalType = $Collection = $PriceMin = $PriceMax = $OrderBy = $OrderType = $PageNumber = $PageSize = $settingId = $caratminvalue =
        $caratmaxvalue = $Filtermode = '';

        if ($requestParam) {

             /*echo "<pre>";
                echo  'asdadad';
                print_r($requestParam);
             exit;*/

             $DealerID = 'DealerID='.$this->getUsername().'&';

                
            if (array_key_exists('shapes', $requestParam)) {

                if ($requestParam['shapes']) {

                    $Shape = 'Shape='.$requestParam['shapes'].'&';

                }

            }

            if (array_key_exists('ring_metal', $requestParam)) {

                if ($requestParam['ring_metal']) {

                    $ring_metal = str_replace(' ', '+', $requestParam['ring_metal']);

                    $MetalType = 'MetalType='.$ring_metal.'&';

                }

            }

            if (array_key_exists('ring_collection', $requestParam)) {

                if ($requestParam['ring_collection']) {

                      $ring_collection = str_replace(' ', '+', $requestParam['ring_collection']);

                      $Collection = 'Collection='.$ring_collection.'&';

                }

            }

            if (array_key_exists('price_from', $requestParam)) {

                if ($requestParam['price_from']) {

                    $PriceMin = 'PriceMin='.$requestParam['price_from'].'&';

                } else {

                    $PriceMin = 'PriceMin=0&';

                }

            }

            if (array_key_exists('price_to', $requestParam)) {

                if ($requestParam['price_to']) {

                    $PriceMax = 'PriceMax='.$requestParam['price_to'].'&';

                }

            }

                

            if (array_key_exists('sort_by', $requestParam)) {

                if ($requestParam['sort_by']) {

                    $OrderBy = 'OrderBy='.$requestParam['sort_by'].'+'.$requestParam['sort_direction'].'&';

                }

            }


            if (array_key_exists('page_number', $requestParam)) {

                if ($requestParam['page_number']) {

                    $PageNumber = 'PageNumber='.$requestParam['page_number'].'&';

                }

            }

            if (array_key_exists('page_size', $requestParam)) {

                if ($requestParam['page_size']) {

                    $PageSize = 'PageSize='.$requestParam['page_size'];

                }

            }

                

            if (array_key_exists('settingId', $requestParam)) {

                if ($requestParam['settingId']) {

                    $settingId = 'SID='.$requestParam['settingId'].'&';

                }

            }


                /*if (array_key_exists('caratvalue', $requestParam)) {

                    if($requestParam['caratvalue']){

                        $caratvalue = 'centerStoneMaxCarat='.$requestParam['caratvalue'].'&';
                        $caratvalue = '';

                    }

                }*/


            if (array_key_exists('caratminvalue', $requestParam)) {

                if ($requestParam['caratminvalue']) {

                    $caratminvalue = $requestParam['caratminvalue'];

                }

            }


            if (array_key_exists('caratmaxvalue', $requestParam)) {

                if ($requestParam['caratmaxvalue']) {

                    $caratmaxvalue =$requestParam['caratmaxvalue'];

                }

            }


            if (array_key_exists('filtermode', $requestParam)) {

                if ($requestParam['filtermode']) {

                    $Filtermode = '&IsLabSetting=1';

                }

            }
                $diamondcookie = "";
            if ($this->getDiamondCookieData()) {
                    $diamondcookie = "&centerStoneMaxCarat=". $this->getDiamondCookieData()->caratmax . "&centerStoneMinCarat=" . $this->getDiamondCookieData()->caratmin;
            }
                

                  $query_string = $DealerID.$Shape.$Collection.$MetalType.$PriceMin.$PriceMax.$caratminvalue.$caratmaxvalue.$settingId.$OrderBy.$PageNumber.$PageSize.$Filtermode.$diamondcookie;

                  $requestUrl = $this->getmountinglistapi().$query_string;


        }

           /* echo $requestUrl;
            exit;*/
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());

        $responce = curl_exec($curl);

        $results = json_decode($responce);

        if (curl_errno($curl)) {

            $this->logger->error('Gemfind: An error has occurred. ');

            return $returnData = ['rings' => [], 'total' => 0];

        }

        if ($results->message !='Successfull') {

            $this->logger->error('Gemfind : An error has occurred.');

            return $returnData = ['rings' => [], 'total' => 0];

        }

        curl_close($curl);


        if ($results->mountingList != "" && $results->count > 0) {

            $returnData = ['rings' => $results->mountingList, 'total' => $results->count];

        } else {

            $returnData = ['rings' => [], 'total' => 0];

        }

        return $returnData;
    }





    /**

     * @param $requestParam

     * @return array

     */

    public function sendRequest($requestParam)
    {



        $Shape = $CaratMin = $CaratMax = $PriceMin = $PriceMax = $ColorId = $ClarityId = $CutGradeId = $TableMin = $TableMax = $DepthMin = $DepthMax = $SymmetryId = $PolishId = $FluorescenceId = $Certificate = $OrderBy = $OrderType = $PageNumber = $PageSize = $InHouseOnly = $SOrigin = $query_string = $DID = $FancyColor = $intIntensity = $HasVideo = '';

        if ($requestParam) {

                

             $DealerID = 'DealerID='.$this->getUsername().'&';

                

            if (array_key_exists('shapes', $requestParam)) {

                if ($requestParam['shapes']) {

                    $Shape = 'Shape='.$requestParam['shapes'].'&';

                }

            }

            if (array_key_exists('size_from', $requestParam)) {

                if ($requestParam['size_from']) {

                    $CaratMin = 'CaratMin='.$requestParam['size_from'].'&';

                }

            }

            if (array_key_exists('size_to', $requestParam)) {

                if ($requestParam['size_to']) {

                    $CaratMax = 'CaratMax='.$requestParam['size_to'].'&';

                }

            }

            if (array_key_exists('price_from', $requestParam)) {

                if ($requestParam['price_from']) {

                    $PriceMin = 'PriceMin='.$requestParam['price_from'].'&';

                } else {

                    $PriceMin = 'PriceMin=0&';

                }

            }

            if (array_key_exists('price_to', $requestParam)) {

                if ($requestParam['price_to']) {

                    $PriceMax = 'PriceMax='.$requestParam['price_to'].'&';

                }

            }

            if (array_key_exists('depth_percent_from', $requestParam)) {

                if ($requestParam['depth_percent_from']) {

                    $DepthMin = 'DepthMin='.$requestParam['depth_percent_from'].'&';

                } else {

                    $DepthMin = 'DepthMin=0&';

                }

            }

            if (array_key_exists('depth_percent_to', $requestParam)) {

                if ($requestParam['depth_percent_to']) {

                    $DepthMax = 'DepthMax='.$requestParam['depth_percent_to'].'&';

                }

            }

            if (array_key_exists('diamond_table_from', $requestParam)) {

                if ($requestParam['diamond_table_from']) {

                    $TableMin = 'TableMin='.$requestParam['diamond_table_from'].'&';

                } else {

                    $TableMin = 'TableMin=0&';

                }

            }

            if (array_key_exists('diamond_table_to', $requestParam)) {

                if ($requestParam['diamond_table_to']) {

                    $TableMax = 'TableMax='.$requestParam['diamond_table_to'].'&';

                }

            }

            if (array_key_exists('color', $requestParam)) {

                if ($requestParam['color']) {

                    $ColorId = 'ColorId='.$requestParam['color'].'&';

                }

            }

            if (array_key_exists('clarity', $requestParam)) {

                if ($requestParam['clarity']) {

                    $ClarityId = 'ClarityId='.$requestParam['clarity'].'&';

                }

            }

            if (array_key_exists('cut', $requestParam)) {

                if ($requestParam['cut']) {

                    $CutGradeId = 'CutGradeId='.$requestParam['cut'].'&';

                }

            }

            if (array_key_exists('symmetry', $requestParam)) {

                if ($requestParam['symmetry']) {

                    $SymmetryId = 'SymmetryId='.$requestParam['symmetry'].'&';

                }

            }

            if (array_key_exists('polish', $requestParam)) {

                if ($requestParam['polish']) {

                    $PolishId = 'PolishId='.$requestParam['polish'].'&';

                }

            }

            if (array_key_exists('fluorescence_intensities', $requestParam)) {

                if ($requestParam['fluorescence_intensities']) {

                    $FluorescenceId = 'FluorescenceId='.$requestParam['fluorescence_intensities'].'&';

                }

            }

            if (array_key_exists('labs', $requestParam)) {

                if ($requestParam['labs']) {

                    $Certificate = 'Certificate='.$requestParam['labs'].'&';

                }

            }

            if (array_key_exists('sort_by', $requestParam)) {

                if ($requestParam['sort_by']) {
                    $OrderBy = 'OrderBy=' . $requestParam['sort_by'] . '&';
                }
                if ($requestParam['sort_by'] == 'Inhouse') {
                    $OrderBy .= 'ShowInhouseFirst=true&';
                }

            }

            if (array_key_exists('sort_direction', $requestParam)) {

                if ($requestParam['sort_direction']) {

                    $OrderType = 'OrderType='.$requestParam['sort_direction'].'&';

                }

            }

            if (array_key_exists('page_number', $requestParam)) {

                if ($requestParam['page_number']) {

                    $PageNumber = 'PageNumber='.$requestParam['page_number'].'&';

                }

            }

            if (array_key_exists('page_size', $requestParam)) {

                if ($requestParam['page_size']) {

                    $PageSize = 'PageSize='.$requestParam['page_size'];

                }

            }

            if (array_key_exists('InHouseOnly', $requestParam)) {

                if ($requestParam['InHouseOnly']) {

                    $InHouseOnly = '&InHouseOnly='.$requestParam['InHouseOnly'];

                }

            }

            if (array_key_exists('origin', $requestParam)) {

                if ($requestParam['origin']) {

                    $SOrigin = '&SOrigin='.$requestParam['origin'].'&';

                }

            }



            if (array_key_exists('did', $requestParam)) {

                if ($requestParam['did']) {

                    $DID = 'DID='.$requestParam['did'].'&';

                }

            }





            if (array_key_exists('hasvideo', $requestParam)) {

                if ($requestParam['hasvideo']) {

                    $HasVideo = 'HasVideo='.$requestParam['hasvideo'].'&';

                }

            }

                



            if (array_key_exists('Filtermode', $requestParam)) {

                if ($requestParam['Filtermode'] != 'navstandard' && $requestParam['Filtermode'] != 'navlabgrown') {

                    if (array_key_exists('FancyColor', $requestParam)) {

                        if ($requestParam['FancyColor']) {

                            $FancyColor = 'FancyColor='.$requestParam['FancyColor'].'&';

                        }

                    }

                    if (array_key_exists('intIntensity', $requestParam)) {

                        if ($requestParam['intIntensity']) {

                            $requestParam['intIntensity'] = str_replace(' ', '+', $requestParam['intIntensity']);

                            $intIntensity = 'intIntensity='.$requestParam['intIntensity'].'&';



                        }

                    }

                    $IsLabGrown = '&IsLabGrown=false';

                    $query_string = $DealerID.$Shape.$CaratMin.$CaratMax.$PriceMin.$PriceMax.$ClarityId.$CutGradeId.$TableMin.$TableMax.$DepthMin.$DepthMax.$SymmetryId.$PolishId.$FluorescenceId.$FancyColor.$intIntensity.$Certificate.$SOrigin.$DID.$OrderBy.$OrderType.$PageNumber.$PageSize.$InHouseOnly.$IsLabGrown;

                    $requestUrl = $this->getdiamondlistapifancy().$query_string;

                } else {

                    if ($requestParam['Filtermode'] == 'navlabgrown') {

                        $IsLabGrown = '&IsLabGrown=true';

                    } else {

                        $IsLabGrown = '&IsLabGrown=false';

                    }

                    $query_string = $DealerID.$Shape.$CaratMin.$CaratMax.$PriceMin.$PriceMax.$ColorId.$ClarityId.$CutGradeId.$TableMin.$TableMax.$DepthMin.$DepthMax.$SymmetryId.$PolishId.$FluorescenceId.$Certificate.$SOrigin.$DID.$OrderBy.$OrderType.$PageNumber.$PageSize.$InHouseOnly.$IsLabGrown;

                    $requestUrl = $this->getdiamondlistapi().$query_string;

                }

            }



        }

/*        echo $requestUrl;
        exit;*/
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());

        $responce = curl_exec($curl);

        $results = json_decode($responce);

        if (curl_errno($curl)) {

            $this->logger->error('Gemfind: An error has occurred. ');

            return $returnData = ['diamonds' => [], 'total' => 0];

        }

        if (isset($results->message)) {

            $this->logger->error('Gemfind : An error has occurred.');

            return $returnData = ['diamonds' => [], 'total' => 0];

        }

        curl_close($curl);

        if ($results->diamondList != "" && $results->count > 0) {

            $returnData = ['diamonds' => $results->diamondList, 'total' => $results->count];

        } else {

            $returnData = ['diamonds' => [], 'total' => 0];

        }

        return $returnData;
    }







    /**

     * @param $id

     * @return array

     */

    public function getRingById($id)
    {

        $DealerID = 'DealerID='.$this->getUsername().'&';

        $DID = 'SID='.$id;

        $query_string = $DealerID.$DID;

        $requestUrl = $this->getmountingdetailapi().$query_string;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());

        $responce = curl_exec($curl);

        $results = json_decode($responce);

        

        if (curl_errno($curl)) {

            $this->logger->error('Gemfind: An error has occurred.');

            return $returnData = ['ringData' => [], 'total' => 0];

        }

        if (isset($results->message)) {

            $this->logger->error('Gemfind : An error has occurred.');

            return $returnData = ['ringData' => [], 'total' => 0];

        }

        curl_close($curl);

        if ($results->settingId != "" && $results->settingId > 0) {

            $ringData = (array) $results;

            $returnData = ['ringData' => $ringData];

        } else {

            $returnData = ['ringData' => []];

        }

        return $returnData;
    }



    /**

     * @param $id

     * @return array

     */

    public function getDiamondById($id)
    {

        $DealerID = 'DealerID='.$this->getUsername().'&';

        $DID = 'DID='.$id;

        $query_string = $DealerID.$DID;

        $requestUrl = $this->getdiamonddetailapi().$query_string;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);
        
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());

        $responce = curl_exec($curl);

        $results = json_decode($responce);

        

        if (curl_errno($curl)) {

            $this->logger->error('Gemfind: An error has occurred.');

            return $returnData = ['diamondData' => [], 'total' => 0];

        }

        if (isset($results->message)) {

            $this->logger->error('Gemfind : An error has occurred.');

            return $returnData = ['diamondData' => [], 'total' => 0];

        }

        curl_close($curl);

        if ($results->diamondId != "" && $results->diamondId > 0) {

            $diamondData = (array) $results;

            $returnData = ['diamondData' => $diamondData];

        } else {

            $returnData = ['diamondData' => []];

        }

        return $returnData;
    }



    /**

     * @param $id

     * @param $type

     * @return array

     */

    public function getDiamondByIdtype($id, $type)
    {

        $IslabGrown = '';

        if ($type == 'labcreated') {
            $IslabGrown = '&IslabGrown=true';
        } elseif ($type == 'fancy') {
            $IslabGrown = '&IsFancy=true';
        } else {
            $IslabGrown = "";
        }

        $DealerID = 'DealerID='.$this->getUsername().'&';

        $DID = 'DID='.$id;

        $query_string = $DealerID.$DID.$IslabGrown;

        $requestUrl = $this->getdiamonddetailapi().$query_string;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());

        $responce = curl_exec($curl);

        $results = json_decode($responce);

        

        if (curl_errno($curl)) {

            $this->logger->error('Gemfind: An error has occurred.');

            return $returnData = ['diamondData' => [], 'total' => 0];

        }

        if (isset($results->message)) {

            $this->logger->error('Gemfind : An error has occurred.');

            return $returnData = ['diamondData' => [], 'total' => 0];

        }

        curl_close($curl);

        if ($results->diamondId != "" && $results->diamondId > 0) {

            $diamondData = (array) $results;

            $returnData = ['diamondData' => $diamondData];

        } else {

            $returnData = ['diamondData' => []];

        }

        return $returnData;
    }





    /**

     * @param $color

     * @return array

     */

    public function getShapeByColor($color)
    {

        $DealerID = 'DealerID='.$this->getUsername().'&';

        $Color = 'Color='.$color;

        $query_string = $DealerID.$Color;

        $requestUrl = $this->getdiamondshapeapi().$query_string;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());

        $responce = curl_exec($curl);

        $results = (array) json_decode($responce);

        

        if (curl_errno($curl)) {

            return $returnData = ['shapes' => [], 'total' => 0];

        }

        if (($results[0]->status == 0)) {

            return $returnData = ['shapes' => [], 'total' => 0];

        }

        

        if (($results[0]->status > 0) && ($results[0]->message == 'Success')) {

            foreach ($results[1][0]->shapes as $value) {

                $value = (array) $value;

                $shapes[] = strtolower($value['shapeName']);

            }

            $returnData = ['shapes' => $shapes, 'total' => sizeof($shapes)];

            return $returnData;

        }
    }





    /**

     * @param $color

     * @return array

     */

    public function getActiveNavigation()
    {

        $DealerID = 'DealerID='.$this->getUsername();

        $requestUrl = $this->getnavigationapi().$DealerID;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());

        $responce = curl_exec($curl);

        $results = (array) json_decode($responce);

        if (isset($results[0])) {

            $results = (array) $results[0];

        

            if (curl_errno($curl)) {

                return $returnData = ['navigation' => [], 'total' => 0];

            }



            if (sizeof($results) == 0) {

                return $returnData = ['navigation' => [], 'total' => 0];

            }

        

            if (sizeof($results) > 0) {

                foreach ($results as $name => $value) {

                    if ($name != '$id' && $name != 'navAdvanced' && $name != 'navRequest') {

                        $navigation[$name] = $value;

                    }

                }

                $returnData = ['navigation' => $navigation, 'total' => sizeof($navigation)];

                return $returnData;

            }

        } else {

            return $returnData = ['navigation' => [], 'total' => 0];

        }
    }



    /**

     * @return array

     */

    public function getStyleSetting()
    {

        $DealerID = 'DealerID='.$this->getUsername();

        $query_string = $DealerID.'&ToolName=DL';

        $requestUrl = $this->getStyleSettingapi().$query_string;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());

        $responce = curl_exec($curl);

        $results = (array) json_decode($responce);

        if (curl_errno($curl)) {

            return $returnData = ['settings' => [],];

        }

        if (isset($results[0][0])) {

            $settings = (array) $results[0][0];

            $returnData = ['settings' => $settings,];

            return $returnData;

        }
    }


    /**

     * @return array

     */

    public function getStyleSettingRB()
    {

        $DealerID = 'DealerID='.$this->getUsername();

        $ToolType = '&ToolName=RB';

        $query_string = $DealerID.$ToolType;


        $requestUrl = $this->getStyleSettingapi().$query_string;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());

        $responce = curl_exec($curl);

        $results = (array) json_decode($responce);

        if (curl_errno($curl)) {

            return $returnData = ['settings' => [],];

        }

        if (isset($results[0][0])) {

            $settings = (array) $results[0][0];

            $returnData = ['settings' => $settings,];

            return $returnData;

        }
    }

    public function getSettingcaratRange($carat = null)
    {

        $caratRange = $this->scopeConfig->getValue(
            self::XML_PATH_CARAT_RANGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($carat) {
            $caratRangeArray = (array)json_decode($caratRange);
            foreach ($caratRangeArray as $_range) {
                if ($carat >= $_range[0] && $carat <= $_range[1]) {
                    $caratRange = json_encode([$carat => $_range]);
                }
            }
        }

        return $caratRange;
    }
    public function getCsvPath()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CSV_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getJCOptiondata()
    {
        $DealerID = 'DealerID='.$this->getUsername();
        $query_string = $DealerID;
        $requestUrl = $this->getJCoptionsapi().$query_string;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getApiTimeout());
        $responce = curl_exec($curl);
        
        $results = (array) json_decode($responce);
        //echo "<pre>";print_r($results);die;
        if (curl_errno($curl)) {
            return $returnData = ['data' => [],];
        }
        if (isset($results[0][0])) {
            $settings = (array) $results[0][0];
            $returnData = ['data' => $settings,];
            return $returnData;
        }
    }


    /**
     * @return boolean
     */
    public function isDiamondSold($sku)
    {
        $statuses = ["canceled"] ;

        $collection = $this->_orderCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', ['nin' => $statuses])
            ->addFieldToFilter('total_paid', ['gt' => 0])
            ->setOrder('created_at', 'desc');

        $collection->getSelect()
        ->join(
            ["sales_order_item" => "sales_order_item"],
            'main_table.entity_id = sales_order_item.order_id',
            ['sku','product_id']
        )
        ->where('sales_order_item.sku = ?', $sku);
        
        foreach ($collection as $item) {
            $productId = $item->getProductId();
            $product = $this->product->load($productId);
            $attributeSet = $this->attributeSetRepository->get($product->getAttributeSetId());
            if ($attributeSet->getAttributeSetName() == "Gemfind Diamonds") {
                return true;
            }
        }

        return false;
    }
}
