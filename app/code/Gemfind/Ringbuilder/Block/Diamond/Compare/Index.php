<?php



namespace Gemfind\Ringbuilder\Block\Diamond\Compare;



use Gemfind\Ringbuilder\Helper\Data as Helper;



class Index extends \Magento\Framework\View\Element\Template {


    const COOKIE_NAME_RING = 'ringsetting';

    /**

     * Store manager

     *

     * @var \Magento\Store\Model\StoreManagerInterface

     */

    protected $_storeManager;



    /**

     * @var Helper

     */

    protected $helper;    





    /**

     * Compare Index constructor.

     * @param Context $context

     * @param StoreManagerInterface $storeManager

     * @param Helper $helper

     * @param array $data

     */

    public function __construct(

    \Magento\Framework\View\Element\Template\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, Helper $helper, \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager, \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,  array $data = []

    ) {

        parent::__construct($context, $data);

        $this->_storeManager = $storeManager;

        $this->helper = $helper;

        $this->_cookieManager = $cookieManager;

        $this->_cookieMetadataFactory = $cookieMetadataFactory;

    }



    /**

     * @return string

     */

    public function getCurrencySymbol() {

        $dealerID = $this->helper->getUsername();

        $requestUrl = $this->helper->getfilterapi().'DealerID='.$dealerID;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestUrl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_TIMEOUT, $this->helper->getApiTimeout());

        $responce = curl_exec($curl);

        $results = (array) json_decode($responce);

        if(sizeof($results) > 1 && $results[0]->message == 'Success'){

            foreach ($results[1] as $value) {

                return $value->currencyFrom.$value->currencySymbol;

            }

        }

        curl_close($curl);

    }

    /**

     * @return string

     */

    public function getDiamondviewurl($param,$type)
    {
        return $this->getUrl('ringbuilder/diamond/view', ['path' => $param, 'type' => $type, '_secure' => true]);
    }

    /**

     * @return string

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
     * @return mixed
    */
    public function getRingCookieData(){
       $ringsettingcookie = json_decode($this->_cookieManager->getCookie('saveringbackvalue'));     
       return $ringsettingcookie;     
    }


    /**
     * @return bool
     */
    public function getGemfindEnabledPoweredBy()
    {   
        return $this->helper->isGemfindEnabledPoweredBy();
    }

}

