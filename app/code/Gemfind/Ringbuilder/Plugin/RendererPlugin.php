<?php
namespace Gemfind\Ringbuilder\Plugin;

class RendererPlugin
{
    /** @var \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet **/
    protected $attributeSet;

    /**
     * @var Helper
     */
    protected $helper;

    /** @var \Magento\Catalog\Model\ProductFactory $_productloader **/
    protected $_productloader;

    /**
     * afterGetProductUrl constructor.
     *
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     * @param \Gemfind\Diamondsearch\Helper\Data $helper
     */
    public function __construct(
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Gemfind\Ringbuilder\Helper\Data $helper
    ) {
        $this->attributeSet = $attributeSet;
        $this->_productloader = $_productloader;
        $this->helper = $helper;
    }

    public function afterGetProductUrl(\Magento\Checkout\Block\Cart\Item\Renderer $subject, $result)
    {
        $attributeSetRepository = $this->attributeSet->get($subject->getProduct()->getAttributeSetId());
        if ($attributeSetRepository->getAttributeSetName() == "Gemfind Diamonds") {
            $diamondtype = '';
            $product = $this->_productloader->create()->load($subject->getProduct()->getId());
            if ($product->getGemfindDiamondType()) {
                $diamondtype = '/type/'.$product->getGemfindDiamondType();
            }

            return $subject->getBaseUrl().'ringbuilder/diamond/view/path/'.$subject->getProduct()->getUrlKey().$diamondtype;
        }

        if ($attributeSetRepository->getAttributeSetName() == "Gemfind Ringbuilder") {
            return $subject->getBaseUrl().'ringbuilder/settings/view/path/'.$subject->getProduct()->getUrlKey();
        }

        if ($attributeSetRepository->getAttributeSetName() == "Ringbuilder") {
            $product = $subject->getProduct();
            return "{$subject->getBaseUrl()}ringbuilder/settings/view/path/" .
                "{$product->getUrlKey()}-sku-{$product->getSku()}";
        }

        return $result;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Item\Renderer $subject
     * @param $result
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function afterGetOptionList(\Magento\Checkout\Block\Cart\Item\Renderer $subject, $result)
    {
        $attributeSetRepository = $this->attributeSet->get($subject->getProduct()->getAttributeSetId());

        if ($attributeSetRepository->getAttributeSetName() == "Gemfind Diamonds") {

            $diamond_id = str_replace('dl-', '', $subject->getProduct()->getSku());
            $diamondData = $this->helper->getDiamondById($diamond_id);

            $test = [];
            $test[1] =  ['label' => 'Color','value' => 'NA'];
            if (isset($diamondData['diamondData']['color'])) {
                $test[1] =  ['label' => 'Color','value' => ($diamondData['diamondData']['color']) ? $diamondData['diamondData']['color'] : 'NA'];
            }

            $test[2] =  ['label' => 'Clarity','value' => 'NA'];
            if (isset($diamondData['diamondData']['clarity'])) {
                $test[2] =  ['label' => 'Clarity','value' => ($diamondData['diamondData']['clarity']) ? $diamondData['diamondData']['clarity'] : 'NA'];
            }

            $test[3] =  ['label' => 'Cut','value' => 'NA'];
            if (isset($diamondData['diamondData']['cut'])) {
                $test[3] =  ['label' => 'Cut','value' => ($diamondData['diamondData']['cut']) ? $diamondData['diamondData']['cut'] : 'NA'];
            }

            $test[4] =  ['label' => 'Carat','value' => 'NA'];
            if (isset($diamondData['diamondData']['caratWeight'])) {
                $test[4] =  ['label' => 'Carat','value' => ($diamondData['diamondData']['caratWeight']) ? $diamondData['diamondData']['caratWeight'] : 'NA'];
            }

            $test[5] =  ['label' => 'Certificate','value' => 'NA'];
            if (isset($diamondData['diamondData']['certificate'])) {
                $test[5] =  ['label' => 'Certificate','value' => ($diamondData['diamondData']['certificate']) ? $diamondData['diamondData']['certificate'] : 'NA'];
            }

            return $test;
        }

        return $result;
    }
}
