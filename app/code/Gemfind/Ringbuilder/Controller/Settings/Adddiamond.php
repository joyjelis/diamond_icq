<?php



namespace Gemfind\Ringbuilder\Controller\Settings;

use Magento\Framework\App\Action\Action;

use Magento\Catalog\Model\Product;

use Magento\Catalog\Model\Product\Type;

use Gemfind\Ringbuilder\Helper\Data as Helper;

use Magento\Framework\View\Result\PageFactory;

use Magento\Framework\App\Action\Context;

use Magento\Store\Model\StoreManagerInterface;

class Adddiamond extends Action
{

    const DIAMOND_COOKIE_NAME = 'diamondsetting';

    const COOKIE_NAME = 'ringsetting';

    const COOKIE_DURATION = 86400; // lifetime in seconds (24 hour)



    /**

     * @var PageFactory

     */

    protected $resultPageFactory;



    /**

     * @var Product

     */

    protected $product;



    /**

     * @var Helper

     */

    protected $helper;



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



    /**

     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory

     */

    protected $_cookieMetadataFactory;



    /**

     * Add constructor.

     * @param Context $context

     * @param PageFactory $resultPageFactory

     * @param Product $product

     * @param Helper $helper

     * @param StoreManagerInterface $storeManager

     * @param CookieManagerInterface $cookieManager

     * @param CookieMetadataFactory $cookieMetadataFactory

     */

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Product $product,
        Helper $helper,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {

        $this->resultPageFactory = $resultPageFactory;

        $this->product = $product;

        $this->helper = $helper;

        $this->_storeManager = $storeManager;

        $this->_cookieManager = $cookieManager;

        $this->_cookieMetadataFactory = $cookieMetadataFactory;

        parent::__construct($context);
    }



    public function execute()
    {

        $id = $this->getRequest()->getParam('id');

        $resultRedirect = $this->resultRedirectFactory->create();

        $resultRedirect->setRefererOrBaseUrl();

        if (!$id) {

            $this->messageManager->addError(__('Invalid Request'));

            return $resultRedirect;

        }

        $data = $this->getRequest()->getPostValue();

        if (isset($data['ringcenterstone'])) {

            if (strrchr($data['ringcenterstone'], "[")) {
                
                $caratrangearray = $this->helper->getSettingcaratRange($data['ringcenterstone']);
                
                $caratrangearray = (array) json_decode($caratrangearray);
                
                if (isset($caratrangearray[$data['ringcenterstone']])) {

                    $caratminval = $caratrangearray[$data['ringcenterstone']][0];

                    $caratmaxval = $caratrangearray[$data['ringcenterstone']][1];

                } else {
                    
                    $caratval = $data['ringmincarat'];

                    $caratval = number_format((float)$caratval, 2, '.', '');
                    
                    $caratminval = $caratval;

                    $caratmaxval = $caratval;

                }
            } elseif (isset($data['ringmincarat'])) {

                $caratval = $data['ringmincarat'];

                $caratval = number_format((float)$caratval, 2, '.', '');

                $caratrangearray = $this->helper->getSettingcaratRange($caratval);

                $caratrangearray = (array) json_decode($caratrangearray);

                if (isset($caratrangearray[$caratval])) {

                    $caratminval = $caratrangearray[$caratval][0];

                    $caratmaxval = $caratrangearray[$caratval][1];
                    
                } else {

                    $caratminval = $caratval;

                    $caratmaxval = $caratval;

                }
            }

        } elseif (isset($data['ringmincarat'])) {
            
            $caratval = $data['ringmincarat'];
            $caratval = number_format((float)$caratval, 2, '.', '');
            $caratminval = $caratval;
            $caratmaxval = $caratval;
            if (isset($data['ringmaxcarat']) && !empty($data['ringmaxcarat'])) {
                $caratmaxval = number_format((float) $data['ringmaxcarat'], 2, '.', '');
            }

        }

        $ringinfo = ['settingid' => $id, 'ringsize' => $data['ringsizewithdia'], 'shapes' => $id, 'caratmax' => $caratmaxval, 'caratmin' => $caratminval, 'centerstonefit' => strtolower($data['centerStoneFit']), 'ringfiltermode' => strtolower($data['ringfiltermode']) ];


        $ringinfo = json_encode($ringinfo);

        $metadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()->setDuration(self::COOKIE_DURATION)->setPath('/');

        $this->_cookieManager->setPublicCookie(self::COOKIE_NAME, $ringinfo, $metadata);

        if ($this->_cookieManager->getCookie(self::DIAMOND_COOKIE_NAME)) {

            $this->_redirect("ringbuilder/diamond/completering");

        } else {

            if ($data['ringfiltermode']) {
                return $resultRedirect->setUrl($this->_url->getUrl('ringbuilder/diamond').'type/navlabgrown');
            } else {
                $this->_redirect("ringbuilder/diamond");
            }
        }
    }
}
