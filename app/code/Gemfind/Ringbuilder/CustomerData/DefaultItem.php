<?php



namespace Gemfind\Ringbuilder\CustomerData;



/**

 * Default item

 */

class DefaultItem extends \Magento\Checkout\CustomerData\DefaultItem

{

    /**

     * @var \Magento\Catalog\Helper\Image

     */

    protected $imageHelper;



    /**

     * @var \Magento\Msrp\Helper\Data

     */

    protected $msrpHelper;



    /**

     * @var \Magento\Framework\UrlInterface

     */

    protected $urlBuilder;



    /**

     * @var \Magento\Catalog\Helper\Product\ConfigurationPool

     */

    protected $configurationPool;



    /**

     * @var \Magento\Checkout\Helper\Data

     */

    protected $checkoutHelper;



    /** @var \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet **/

    protected $attributeSet;

    /** @var \Magento\Catalog\Model\ProductFactory $_productloader **/
    protected $_productloader; 



    /**

     * @param \Magento\Catalog\Helper\Image $imageHelper

     * @param \Magento\Msrp\Helper\Data $msrpHelper

     * @param \Magento\Framework\UrlInterface $urlBuilder

     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool

     * @param \Magento\Checkout\Helper\Data $checkoutHelper

     * @codeCoverageIgnore

     */

    public function __construct(

        \Magento\Catalog\Helper\Image $imageHelper,

        \Magento\Msrp\Helper\Data $msrpHelper,

        \Magento\Framework\UrlInterface $urlBuilder,

        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,

        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,

        \Magento\Catalog\Model\ProductFactory $_productloader,

        \Magento\Checkout\Helper\Data $checkoutHelper

    ) {

        $this->configurationPool = $configurationPool;

        $this->imageHelper = $imageHelper;

        $this->msrpHelper = $msrpHelper;

        $this->urlBuilder = $urlBuilder;

        $this->attributeSet = $attributeSet;

        $this->_productloader = $_productloader;

        $this->checkoutHelper = $checkoutHelper;

    }



    protected function doGetItemData()

    {   

        $attributeSetRepository = $this->attributeSet->get($this->item->getProduct()->getAttributeSetId());

        $flag = $producturl = "";

            if($attributeSetRepository->getAttributeSetName() == "Gemfind Diamonds"){

                $flag = 1;

                $product = $this->_productloader->create()->load($this->item->getProduct()->getId());
                if($product->getGemfindDiamondType()){
                    $diamondtype = '/type/'.$product->getGemfindDiamondType();
                } else{
                    $diamondtype = '';
                }

                $producturl = $this->getUrl('ringbuilder/diamond/view', ['path' => $this->item->getProduct()->getUrlKey(), '_secure' => true]).$diamondtype;

            }



            if($attributeSetRepository->getAttributeSetName() == "Gemfind Ringbuilder"){

                $flag = 1;

                $producturl = $this->getUrl('ringbuilder/settings/view', ['path' => $this->item->getProduct()->getUrlKey(), '_secure' => true]);

            }

        $imageHelper = $this->imageHelper->init($this->getProductForThumbnail(), 'mini_cart_product_thumbnail');

        return [

            'options' => $this->getOptionList(),

            'qty' => $this->item->getQty() * 1,

            'item_id' => $this->item->getId(),

            'configure_url' => $this->getConfigureUrl(),

            'is_visible_in_site_visibility' => ($flag == 1)?'':$this->item->getProduct()->isVisibleInSiteVisibility(),

            'product_id' => $this->item->getProduct()->getId(),

            'product_name' => $this->item->getProduct()->getName(),

            'product_sku' => $this->item->getProduct()->getSku(),

            'product_url' => ($flag == 1)?$producturl:$this->getProductUrl(),

            'product_has_url' => $this->hasProductUrl(),

            'product_price' => $this->checkoutHelper->formatPrice($this->item->getCalculationPrice()),

            'product_price_value' => $this->item->getCalculationPrice(),

            'product_image' => [

                'src' => $imageHelper->getUrl(),

                'alt' => $imageHelper->getLabel(),

                'width' => $imageHelper->getWidth(),

                'height' => $imageHelper->getHeight(),

            ],

            'canApplyMsrp' => $this->msrpHelper->isShowBeforeOrderConfirm($this->item->getProduct())

                && $this->msrpHelper->isMinimalPriceLessMsrp($this->item->getProduct()),

        ];

    }



    /**

     * @return string

     */



    public function getUrl($route = '', $params = []) {

        return $this->urlBuilder->getUrl($route, $params);

    }



    /**

     * @return \Magento\Catalog\Model\Product

     * @codeCoverageIgnore

     */

    protected function getProductForThumbnail()

    {

        return $this->item->getProduct();

    }

}

