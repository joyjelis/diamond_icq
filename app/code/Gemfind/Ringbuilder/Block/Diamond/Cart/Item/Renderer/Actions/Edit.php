<?php



namespace Gemfind\Ringbuilder\Block\Diamond\Cart\Item\Renderer\Actions;

/**

 * @api

 */

class Edit extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Edit
{

    /** @var \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet **/

    protected $attributeSet;

    /** @var \Magento\Catalog\Model\ProductFactory $_productloader **/
    protected $_productloader;

    /**
     * Edit constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     * @param array $data
     */
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        array $data = []
    ) {
        $this->attributeSet = $attributeSet;
        $this->_productloader = $_productloader;
        parent::__construct($context, $data);
    }

    /**

     * Get item configure url

     *

     * @return string

     */

    public function getConfigureUrl()
    {

        $attributeSetRepository = $this->attributeSet->get($this->getItem()->getProduct()->getAttributeSetId());

        if ($attributeSetRepository->getAttributeSetName() == "Gemfind Diamonds") {
            $diamondtype = '';
            $product = $this->_productloader->create()->load($this->getItem()->getProduct()->getId());
            if ($product->getGemfindDiamondType()) {
                $diamondtype = 'type/'.$product->getGemfindDiamondType();
            }

            return $this->getUrl('ringbuilder/diamond/view', ['path' => $this->getItem()->getProduct()->getUrlKey(), '_secure' => true]).$diamondtype;

        }



        if ($attributeSetRepository->getAttributeSetName() == "Gemfind Ringbuilder") {

            return $this->getUrl('ringbuilder/settings/view', ['path' => $this->getItem()->getProduct()->getUrlKey(), '_secure' => true]);

        }

        return $this->getUrl(
            'checkout/cart/configure',
            [

                'id' => $this->getItem()->getId(),

                'product_id' => $this->getItem()->getProduct()->getId()

            ]
        );
    }
}
