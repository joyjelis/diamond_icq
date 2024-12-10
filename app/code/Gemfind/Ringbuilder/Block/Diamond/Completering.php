<?php


namespace Gemfind\Ringbuilder\Block\Diamond;

use Magento\Framework\View\Element\Template\Context;
use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class Completering extends Template
{

    const RING_COOKIE_NAME = 'ringsetting';

    const DIAMOND_COOKIE_NAME = 'diamondsetting';
    /**
     * @var string
     */
    protected $_template = 'Gemfind_Ringbuilder::diamond/completering.phtml';

    /**
     * @var Helper
     */
    protected $helper;

    /**
    * @var \Magento\Framework\Stdlib\CookieManagerInterface
    */
    protected $_cookieManager;
    /**


    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * Index constructor.
     * @param Context $context
     * @param Helper $helper
     * @param CookieManagerInterface $cookieManager
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,        
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
    
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->_cookieManager = $cookieManager;
        $this->_storeManager = $storeManager;
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
     * @return string
     */
    public function getAddtocartAction()
    {
        return $this->getUrl('ringbuilder/diamond/index', ['_secure' => true]);
    }

    
    /**
     * @return string
     */
    public function getSelectedRing()
    {
        $ringcookie = $this->_cookieManager->getCookie(self::RING_COOKIE_NAME);
        if($ringcookie){
            $ringcookie = json_decode($ringcookie);
            $ringdata = (array)$this->helper->getRingById($ringcookie->settingid);
            $ringdata['selectedData'] = $ringcookie;
            return $ringdata;
        }
        return;
    }

    /**
     * @return string
     */
    public function getSelectedDiamond()
    {
        $diamondcookie = $this->_cookieManager->getCookie(self::DIAMOND_COOKIE_NAME);
        if($diamondcookie){
            $diamondcookie = json_decode($diamondcookie);
            $diamonddata = array();
            if($diamondcookie->diamondtype == 'labcreated'){
                $diamonddata = (array)$this->helper->getDiamondByIdtype($diamondcookie->diamondid, $diamondcookie->diamondtype);
                $diamonddata['diamondData'] += ['type' => 'labcreated'];
                return $diamonddata;
            } else if($diamondcookie->diamondtype == 'fancy'){
                 $diamonddata = (array)$this->helper->getDiamondByIdtype($diamondcookie->diamondid, $diamondcookie->diamondtype);
                 $diamonddata['diamondData'] += ['type' => 'fancy'];
                 return $diamonddata;
            } else {
                return (array)$this->helper->getDiamondById($diamondcookie->diamondid);
            }
            
        }
        return;
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
     * @return string
    */

    public function getAddtocartUrl()
    {
        return $this->getUrl('ringbuilder/diamond/completepurchase', ['_secure' => true]);
    }

        /**
     * @return string
    */
    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
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
	/**

     * @return string

     */

    public function getScheViewFormAction() {

        return $this->getUrl('ringbuilder/settings/completescheview', ['_secure' => true]);

    }
	
	 /**
     * @return string
     */
    public function getFormAction() {
        return $this->getUrl('ringbuilder/settings/completedrophint', ['_secure' => true]);
    }

    /**
     * @return string
     */

    public function getEmailFrndFormAction() {
        return $this->getUrl('ringbuilder/settings/completeemailfriend', ['_secure' => true]);

    }

    /**
     * @return string
     */

    public function getReqInfoFormAction() {

        return $this->getUrl('ringbuilder/settings/completereqinfo', ['_secure' => true]);
    }

    /** 
     * @return string
    */

    public function isCaptchakey() {

        return $this->helper->getCaptchaKey();

    }
}

