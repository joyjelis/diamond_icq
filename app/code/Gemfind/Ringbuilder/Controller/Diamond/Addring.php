<?php



namespace Gemfind\Ringbuilder\Controller\Diamond;

use Magento\Framework\App\Action\Action;

use Magento\Catalog\Model\Product;

use Magento\Catalog\Model\Product\Type;

use Gemfind\Ringbuilder\Helper\Data as Helper;

use Magento\Framework\View\Result\PageFactory;

use Magento\Framework\App\Action\Context;

use Magento\Store\Model\StoreManagerInterface;

class Addring extends Action
{



    const COOKIE_NAME = 'diamondsetting';

    const RING_COOKIE_NAME = 'ringsetting';

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

        if ($this->helper->isDiamondSold("dl-{$id}")) {
            $this->messageManager->addError(__('The diamond you selected is no longer available or already sold!'));
            return $resultRedirect;
        }

        $data = $this->getRequest()->getPostValue();

        $centerstone = $carat = $type = $caratminval = $caratmaxval = '';


        if (isset($data['centerstone'])) {

            $centerstone = $data['centerstone'];

        }

        if (isset($data['carat'])) {

            $carat = $data['carat'];

            $caratrangearray = $this->helper->getSettingcaratRange($carat);
            
            $caratrangearray = (array) json_decode($caratrangearray);
            
            if (isset($caratrangearray[$data['carat']])) {

                $caratminval = $caratrangearray[$data['carat']][0];

                $caratmaxval = $caratrangearray[$data['carat']][1];

            } else {
                
                $caratval = $data['carat'];
                
                $caratval = number_format((float)$caratval, 2, '.', '');
                
                $caratminval = (int)$caratval - 0.1;

                $caratmaxval = (int)$caratval + 0.1;

            }
        }

        if (isset($data['type'])) {
            $type = $data['type'];
        }

        $diamondinfo = ['diamondid' => $id,'centerstone' => $centerstone,'carat' => $carat,'caratmin' => $caratminval,'caratmax' => $caratmaxval, 'diamondtype' => $type];
        
        $diamondinfo = json_encode($diamondinfo);

        $metadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()->setDuration(self::COOKIE_DURATION)->setPath('/');

        $this->_cookieManager->setPublicCookie(self::COOKIE_NAME, $diamondinfo, $metadata);
 
        if ($this->_cookieManager->getCookie(self::RING_COOKIE_NAME)) {

            $this->_redirect("ringbuilder/diamond/completering");

        } else {

            $this->_redirect("ringbuilder/settings");

        }
    }
}
