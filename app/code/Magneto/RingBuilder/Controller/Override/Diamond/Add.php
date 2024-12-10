<?php

namespace Magneto\RingBuilder\Controller\Override\Diamond;

use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;

class Add extends \Gemfind\Ringbuilder\Controller\Diamond\Add
{

    public function execute()
    {

        $id = $this->getRequest()->getParam('id');

        $resultRedirect = $this->resultRedirectFactory->create();

        $resultRedirect->setRefererOrBaseUrl();

        if (!$id) {

            $this->messageManager->addError(__('Invalid Request'));

            return $resultRedirect;

        }

        try {

            $attributeSetId = $this->attributeSet->load(self::ATTRIBUTE_SET, 'attribute_set_name')->getAttributeSetId();

            if (!$attributeSetId) {

                $this->messageManager->addError(__('Invalid Attribute Set'));

                return $resultRedirect;

            }
            $post = $this->getRequest()->getPostValue();

            if (isset($post['type'])) {
                $diamondtype = $post['type'];
                $diamondData = $this->helper->getDiamondByIdtype($id, $post['type']);
            } else {
                $diamondtype = '';
                $diamondData = $this->helper->getDiamondById($id);
            }

            if (!count($diamondData)) {

                $this->messageManager->addError(__('Something went wrong. Please try again later!'));

                return $resultRedirect;

            }

            if ($this->helper->isDiamondSold("dl-" . $diamondData['diamondData']['diamondId'])) {
                $this->messageManager->addError(__('The diamond you selected is no longer available or already sold!'));
                return $resultRedirect;
            }

            $shapevalue = $cutvalue = '';

            $diamondName = $diamondData['diamondData']['mainHeader'];

            if (isset($diamondData['diamondData']['shape'])) {

                $attribute = $this->product->getResource()->getAttribute('gemfind_diamond_shape');

                if ($attribute->usesSource()) {

                    $shapevalue = $attribute->getSource()->getOptionId($diamondData['diamondData']['shape']);

                }

            }

            if (isset($diamondData['diamondData']['cut'])) {

                $attribute = $this->product->getResource()->getAttribute('gemfind_diamond_cut');

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

            $urlstring = strtolower($urlshape . $urlcarat . $urlcolor . $urlclarity . $urlcut . $urlcertificate . 'sku-' . $diamondData['diamondData']['diamondId']);

            $product = $this->product;

            if ($product->getIdBySku("dl-" . $diamondData['diamondData']['diamondId'])) {

                $product->load($product->getIdBySku("dl-" . $diamondData['diamondData']['diamondId']));

                $product->setGemfindDiamondType($diamondtype);

                $product->setPrice($this->getPrice($diamondData['diamondData']['fltPrice']));

                $product->setQuantityAndStockStatus(['qty' => 1, 'is_in_stock' => 1]);

                $product->setUrlKey($urlstring);

            } else {

                $productData = [

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

                    'url_key'=> $urlstring,

                    'gemfind_diamond_shape' => $shapevalue,

                    'gemfind_diamond_cut' => $cutvalue,

                    'gemfind_diamond_type' => $diamondtype,

                ];

                $product->setData($productData);

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
                                    $product->addImageToMediaGallery($localImageUrl, $mediaAttribute, false, false);
                                }
                            }
                        }
                    }
                }
            }

            $product->save();

            if ($this->addProductsByIds($product->getId())) {
                $message = __(
                    'You added %1 to your shopping cart.',
                    $product->getName()
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

    public function is_404($url)
    {

        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

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

    public function getPrice($price)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Magneto\RingBuilder\ViewModel\PriceHelper');
        return $helper->convertToBaseCurrencyWithUpdate($price, 0);
    }

    public function addProductsByIds($id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Magneto\RingBuilder\ViewModel\PriceHelper');
        return $helper->addProductsByIds([$id]);
    }
}
