<?php



namespace Gemfind\Ringbuilder\Block\Settings;



use Magento\Framework\View\Element\Template\Context;

use Gemfind\Ringbuilder\Helper\Data as Helper;

use Magento\Framework\Session\SessionManagerInterface;

use Magento\Framework\View\Element\Template;

use Magento\Store\Model\StoreManagerInterface;



class Index extends \Magento\Framework\View\Element\Template

{


    const DIAMOND_COOKIE_NAME = 'diamondsetting';

    const RING_COOKIE_NAME = 'ringsetting';

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

     * cookie manager

     */

    protected $_cookieManager;

    /**

     * Cookie meta data

     */

    protected $_cookieMetadataFactory;


    /**

     * Index constructor.

     * @param Context $context

     * @param Helper $helper

     * @param SessionManagerInterface $sessionManager

     * @param StoreManagerInterface $storeManager

     * @param CookieManagerInterface $cookieManager

     * @param CookieMetadataFactory $cookieMetadataFactory

     * @param array $data

     */

    public function __construct(

        Context $context,

        Helper $helper,

        SessionManagerInterface $sessionManager,

        StoreManagerInterface $storeManager,

        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,

        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,

        array $data = []

    ) {

    

        parent::__construct($context, $data);

        $this->helper = $helper;

        $this->sessionManager = $sessionManager;

        $this->_storeManager = $storeManager;

        $this->_cookieManager = $cookieManager;

        $this->_cookieMetadataFactory = $cookieMetadataFactory;

    }







    /**

     * @return bool

     */

    public function isGemfindEnable()

    {

        return $this->helper->isGemfindEnabled();

    }


    /**

     * @return mixed

     */

    public function getStyleSettingsRB()

    {

        return $this->helper->getStyleSettingRB();

    }

    /**

     * @return mixed

     */

    public function getDealerID()

    {

        return $this->helper->getUsername();

    }

    /**

     * @return mixed

     */

    public function getStyleSettings()

    {

        return;

    }


    /**

     * @return string

     */

    public function getFormAction()

    {

        if ($this->isGemfindEnable()) {

            return $this->getUrl('ringbuilder/settings/ringsearch', ['_secure' => true]);

        }

        return $this->getUrl('ringbuilder/settings', ['_secure' => true]);

    }


    /**

     * @return string

     */

    public function checkDiamondCookie(){

        $metadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()->setPath('/');
                
        //$this->_cookieManager->deleteCookie(self::RING_COOKIE_NAME, $metadata);
        //$this->_cookieManager->deleteCookie(self::DIAMOND_COOKIE_NAME, $metadata);
        //$this->_cookieManager->deleteCookie('saveringbackvalue', $metadata);
        //$this->_cookieManager->deleteCookie('savebackvaluedialabgrown', $metadata);

        $diamondcookie = $this->_cookieManager->getCookie(self::DIAMOND_COOKIE_NAME);

        if($diamondcookie){

            return true;

        } else {

            return false;            

        }

    }

    /**

     * @return Query string parameters

     */

    public function getPara()

    {

        $request = $this->getRequest()->getParams();
		//print_r($_REQUEST);
		if(isset($_GET['viewmode'])){
			$newcookie = array();
			if(isset($_GET['viewmode'])){
				$newcookie['viewmode'] = $_GET['viewmode'];
			}
			if(isset($_GET['currentpage'])){
				$newcookie['currentpage'] = $_GET['currentpage'];
			}
			if(isset($_GET['itemperpage'])){
				$newcookie['itemperpage'] = $_GET['itemperpage'];
			}
			if(isset($_GET['ring_collection'])){
				$newcookie['ringcollection'] = $_GET['ring_collection'];
			}
			if(isset($_GET['price'])){
				$newcookie['PriceMin'] = $_GET['price']['from'];
				$newcookie['PriceMax'] = $_GET['price']['to'];
			}
			if(isset($_GET['filtermode'])){
				$newcookie['Filtermode'] = $_GET['filtermode'];
			}
			if(isset($_GET['orderby'])){
				$newcookie['orderBy'] = $_GET['orderby'];
			}
			if(isset($_GET['ring_metal'])){
				$newcookie['ringmetalList'] = $_GET['ring_metal'];
			}
			if(isset($_GET['settingid'])){
				$newcookie['SID'] = $_GET['settingid'];
			}
			if(isset($_GET['ring_shape'])){
				$newcookie['shapeList'] = $_GET['ring_shape'];
			}
			
			$metadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()->setDuration(86400)->setPath('/');
			$this->_cookieManager->setPublicCookie('saveringfiltercookie', json_encode($newcookie), $metadata); 
		}
			if(isset($request['type'])){
				$metadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()->setDuration(86400)->setPath('/');
				$this->_cookieManager->setPublicCookie('islabsettings', 'labsettings', $metadata);           
				return $request['type'];
			} else{
				$metadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()->setPath('/');
				$this->_cookieManager->deleteCookie('islabsettings', $metadata); 
				return;
			}    
		
    }


    /**
     * @return array
     */
    public function getRingshapedefaultfilter(){
        
        $request = $this->getRequest()->getParams();
        
        if(isset($request['ringshape'])){
        
            return $request['ringshape'];
        
        } else {
            return;    
        }
    }

}