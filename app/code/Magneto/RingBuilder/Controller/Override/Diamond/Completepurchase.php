<?php

namespace Magneto\RingBuilder\Controller\Override\Diamond;

use Gemfind\Ringbuilder\Controller\Diamond\Completepurchase as OriginalController;
use Gemfind\Ringbuilder\Helper\Data as Helper;

use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class Completepurchase extends OriginalController
{
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ProductFactory $product,
        Set $attributeSet,
        Helper $helper,
        StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        File $file,
        CustomerCart $cart,
        \Magento\Catalog\Model\Product\OptionFactory $optioninterface,
        \Magento\Catalog\Api\ProductRepositoryInterface $productrepo,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magneto\RingBuilder\ViewModel\PriceHelper $priceHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->json = $json;
        $this->session = $this->checkoutSession->create();
        $this->quote = $this->session->getQuote();
        $this->priceHelper = $priceHelper;
        parent::__construct(
            $context,
            $resultPageFactory,
            $product,
            $attributeSet,
            $helper,
            $storeManager,
            $directoryList,
            $file,
            $cart,
            $optioninterface,
            $productrepo,
            $cookieManager,
            $cookieMetadataFactory
        );
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */

    public function execute()
    {
        $ringId = $this->getRequest()->getParam('ringId');
        $ringsizesettingonly = $this->getRequest()->getParam('ringsizesettingonly');
        $diamondId = $this->getRequest()->getParam('diamondId');
        $post = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererOrBaseUrl();

        if (!$ringId || !$ringsizesettingonly || !$diamondId) {
            $this->messageManager->addError(__('Invalid Request dsfd'));
            return $resultRedirect;
        }

        try {
            $attributeSetId = $this->attributeSet->load(self::ATTRIBUTE_SET_DIAMONDS, 'attribute_set_name')->getAttributeSetId();

            if (!$attributeSetId) {
                $this->messageManager->addError(__('Invalid Attribute Set'));
                return $resultRedirect;
            }

            if (isset($post['diamondtype'])) {
                $diamondtype = $post['diamondtype'];
                $diamondData = $this->helper->getDiamondByIdtype($diamondId, $post['diamondtype']);
            } else {
                $diamondtype = '';
                $diamondData = $this->helper->getDiamondById($diamondId);
            }

            if (!count($diamondData)) {
                $this->messageManager->addError(__('Something went wrong. Please try again later!'));
                return $resultRedirect;
            }

            if ($this->helper->isDiamondSold("dl-" . $diamondData['diamondData']['diamondId'])) {
                $this->messageManager->addError(__('The diamond you selected is no longer available or already sold!'));
                return $resultRedirect;
            }

            $quoteItems = $this->quote->getItems();
            if (is_array($quoteItems)) {
                foreach ($this->quote->getItems() as $item) {
                    if ($item->getSku() == $ringId) {
                        $error = __('You cannot add the same ring setting to the cart.') . ' SKU# ' . $item->getSku();
                        $this->messageManager->addError($error);
                        return $resultRedirect;
                    } elseif ($item->getSku() == "dl-{$diamondId}") {
                        $error = __('You cannot add the same diamond to the cart.') . ' SKU# ' . $item->getSku();
                        $this->messageManager->addError($error);
                        return $resultRedirect;
                    }
                }
            }

            $this->addRing($ringId, $ringsizesettingonly, $diamondId);

            $shapevalue = $cutvalue = '';

            $diamondName = $diamondData['diamondData']['mainHeader'];

            if (isset($diamondData['diamondData']['shape'])) {
                $attribute = $this->product->create()->getResource()->getAttribute('gemfind_diamond_shape');
                if ($attribute->usesSource()) {
                    $shapevalue = $attribute->getSource()->getOptionId($diamondData['diamondData']['shape']);
                }
            }

            if (isset($diamondData['diamondData']['cut'])) {
                $attribute = $this->product->create()->getResource()->getAttribute('gemfind_diamond_cut');
                if ($attribute->usesSource()) {
                    $cutvalue = $attribute->getSource()->getOptionId($diamondData['diamondData']['cut']);
                }
            }

            if (isset($diamondData['diamondData']['shape'])) {
                $urlshape = str_replace(' ', '-', $diamondData['diamondData']['shape']) . '-shape-';
            } else {
                $urlshape = '';
            }
            if (isset($diamondData['diamondData']['carat'])) {
                $urlcarat = str_replace(' ', '-', $diamondData['diamondData']['carat']) . '-carat-';
            } else {
                $urlcarat = '';
            }
            if (isset($diamondData['diamondData']['color'])) {
                $urlcolor = str_replace(' ', '-', $diamondData['diamondData']['color']) . '-color-';
            } else {
                $urlcolor = '';
            }
            if (isset($diamondData['diamondData']['clarity'])) {
                $urlclarity = str_replace(' ', '-', $diamondData['diamondData']['clarity']) . '-clarity-';
            } else {
                $urlclarity = '';
            }
            if (isset($diamondData['diamondData']['cut'])) {
                $urlcut = str_replace(' ', '-', $diamondData['diamondData']['cut']) . '-cut-';
            } else {
                $urlcut = '';
            }
            if (isset($diamondData['diamondData']['certificate'])) {
                $urlcertificate = str_replace(' ', '-', $diamondData['diamondData']['certificate']) . '-certificate-';
            } else {
                $urlcertificate = '';
            }

            $urlstring = strtolower(
                $urlshape . $urlcarat . $urlcolor . $urlclarity . $urlcut . $urlcertificate .
                'sku-' . $diamondData['diamondData']['diamondId']
            );

            $diamondproduct = $this->product->create();

            if ($diamondproduct->getIdBySku("dl-" . $diamondData['diamondData']['diamondId'])) {
                $diamondproduct->load($diamondproduct->getIdBySku("dl-" . $diamondData['diamondData']['diamondId']));
                $diamondproduct->setGemfindDiamondType($diamondtype);
                $diamondproduct->setPrice($this->getPrice($diamondData['diamondData']['fltPrice']));
                $diamondproduct->setQuantityAndStockStatus(['qty' => 1, 'is_in_stock' => 1]);
                $diamondproduct->setUrlKey($urlstring);
                $diamondproduct->save();
            } else {
                $diamondproductData = [
                    'name' => $diamondName,
                    'description' => $diamondName,
                    'short_description' => $diamondData['diamondData']['subHeader'],
                    'type_id' => Type::TYPE_SIMPLE,
                    'sku' => "dl-" . $diamondData['diamondData']['diamondId'],
                    'attribute_set_id' => $attributeSetId,
                    'weight' => 1,
                    'visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                    'status' => true,
                    'tax_class_id' => 2,
                    'price' => $this->getPrice($diamondData['diamondData']['fltPrice']),
                    'website_ids' => [$this->_storeManager->getWebsite()->getId()],
                    'stock_data' => [
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 0,
                        'is_in_stock' => 1,
                        'min_sale_qty' => 1,
                        'max_sale_qty' => 1,
                        'qty' => 1,
                    ],
                    'url_key' => $urlstring,
                    'gemfind_diamond_shape' => $shapevalue,
                    'gemfind_diamond_cut' => $cutvalue,
                    'gemfind_diamond_type' => $diamondtype,
                ];

                $diamondproduct->setData($diamondproductData);
                if ($diamondData['diamondData']['fancyColorMainBody']) {
                    if (!$this->is_404($diamondData['diamondData']['colorDiamond'])) {
                        $imageurl = $diamondData['diamondData']['colorDiamond'];
                    }
                } else {
                    if (!$this->is_404($diamondData['diamondData']['image2'])) {
                        $imageurl = $diamondData['diamondData']['image2'];
                    }
                }

                if (isset($imageurl)) {
                    $imageUrl = $imageurl;
                    if (!$this->is_404($imageUrl)) {
                        if ($imageUrl) {
                            $pathinfo = @pathinfo($imageurl);
                            $imageType = null;
                            if (isset($pathinfo['extension'])) {
                                $imageType = $pathinfo['extension'];
                            }

                            $filename = null;
                            if (isset($pathinfo['basename'])) {
                                $filename = $diamondData['diamondData']['diamondId'] . $pathinfo['basename'];
                            }

                            if ($imageType && $filename) {
                                $tmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'ringbuilder';

                                $this->file->checkAndCreateFolder($tmpDir);

                                $localImageUrl = $tmpDir . DIRECTORY_SEPARATOR . $filename;
                                $fileContent = file_get_contents(trim($imageUrl));

                                if ($this->file->write($localImageUrl, $fileContent) !== false) {
                                    $mediaAttribute = ['image', 'small_image', 'thumbnail'];
                                    $diamondproduct->addImageToMediaGallery($localImageUrl, $mediaAttribute, false, false);
                                }
                            }
                        }
                    }
                }
                $diamondproduct->save();
            }

            $cart = $this->cart;
            $cart->addProduct($diamondproduct, 1);
            $cart->save();

            //$this->quote->addProduct($diamondproduct, 1);

            //$this->cartRepository->save($this->quote);
            //$this->session->replaceQuote($this->quote)->unsLastRealOrderId();

            $metadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()->setPath('/');
            $this->_cookieManager->deleteCookie(self::COOKIE_NAME_DIAMOND, $metadata);
            $this->_cookieManager->deleteCookie(self::COOKIE_NAME_RING, $metadata);
            $this->_cookieManager->deleteCookie('savebackvaluedia', $metadata);
            $this->_cookieManager->deleteCookie('saveringbackvalue', $metadata);

            if (!$this->cart->getQuote()->getHasError()) {
                $message = __(
                    'Product is successfully added to shopping cart.',
                    $diamondproduct->getName()
                );
                $this->messageManager->addSuccessMessage($message);
            }

            $cartUrl = $this->_storeManager->getStore()->getUrl('checkout/cart');
            return $resultRedirect->setUrl($cartUrl);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    /**
     * @return string
     */

    protected function addRing($id, $ringsize, $diamondId)
    {
        $configProduct = $this->helper->getProductBySku($id);
        $parentProduct = $this->helper->getRingParentProduct($configProduct);

        $diamondproduct = $this->product->create();

        $configProduct->setQuantityAndStockStatus(['qty' => 2, 'is_in_stock' => 1]);
        $configProduct->save();

        $params = [];
        $params['product'] = $parentProduct->getId();
        $params['qty'] = '1';
        $options = [];

        $productAttributeOptions = $parentProduct->getTypeInstance(true)
            ->getConfigurableAttributesAsArray($parentProduct);

        foreach ($productAttributeOptions as $option) {
            $options[$option['attribute_id']] = $configProduct->getData($option['attribute_code']);
        }
        $params['super_attribute'] = $options;

        $_options = ['Ring Size' => false, 'Diamond#' => false];
        foreach ($parentProduct->getOptions() as $o) {
            if (isset($_options[$o->getTitle()])) {
                $_options[$o->getTitle()] = true;
            }
        }

        $arrayOption = [
            "sort_order" => 1,
            "title" => null,
            "price_type" => "fixed",
            "price" => 0.00,
            "type" => "field",
            "is_require" => 0,
        ];
        $customOptions = [];
        foreach ($_options as $_title => $_value) {
            if ($_value === false) {
                $arrayOption['title'] = $_title;

                $customOption = $this->optioninterface->create()
                    ->setProductId($parentProduct->getId())
                    ->setStoreId($parentProduct->getStoreId())
                    ->addData($arrayOption);
                $customOption->save();
                $parentProduct->addOption($customOption);
            }
        }

        $optionsdata = [];
        foreach ($parentProduct->getOptions() as $o) {
            if ($o->getTitle() == 'Ring Size') {
                $optionsdata[$o->getOptionId()] = ($ringsize) ? $ringsize : 'NA';
            }
            if ($o->getTitle() == 'Diamond#') {
                $optionsdata[$o->getOptionId()] = ($diamondId) ? $diamondId : 'NA';
            }
        }

        $params['options'] = $optionsdata;

        $cart = $this->cart;
        $cart->addProduct($parentProduct, $params);

        return;
    }

    public function getPrice($price)
    {
        return $this->priceHelper->convertToBaseCurrencyWithUpdate($price, 0);
    }
}
