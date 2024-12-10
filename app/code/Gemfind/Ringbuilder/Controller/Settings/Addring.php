<?php

namespace Gemfind\Ringbuilder\Controller\Settings;

use Magento\Framework\App\Action\Action;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Checkout\Model\Cart as CustomerCart;

class Addring extends Action
{

    const ATTRIBUTE_SET = 'Gemfind Diamonds';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Set
     */
    protected $attributeSet;

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
     * Directory List
     *
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * File interface
     *
     * @var File
     */
    protected $file;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * Add constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Product $product
     * @param Set $attributeSet
     * @param Helper $helper
     * @param StoreManagerInterface $storeManager
     * @param DirectoryList $directoryList
     * @param File $file
     * @param CustomerCart $cart
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Product $product,
        Set $attributeSet,
        Helper $helper,
        StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        File $file,
        CustomerCart $cart
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->product = $product;
        $this->attributeSet = $attributeSet;
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->cart = $cart;
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

        try {
            $attributeSetId = $this->attributeSet->load(self::ATTRIBUTE_SET, 'attribute_set_name')->getAttributeSetId();

            if (!$attributeSetId) {
                $this->messageManager->addError(__('Invalid Attribute Set'));
                return $resultRedirect;
            }

            $diamondData = $this->helper->getDiamondById($id);

            if (!count($diamondData)) {
                $this->messageManager->addError(__('Something went wrong. Please try again later!'));
                return $resultRedirect;
            }

            $shapevalue = $cutvalue = '';

            $diamondName = $diamondData['diamondData']['mainHeader'];

            if(isset($diamondData['diamondData']['shape'])){
                $attribute = $this->product->getResource()->getAttribute('gemfind_diamond_shape');
                 if ($attribute->usesSource()) {
                      $shapevalue = $attribute->getSource()->getOptionId($diamondData['diamondData']['shape']);
                }    
            }
                        
            if(isset($diamondData['diamondData']['cut'])){
                $attribute = $this->product->getResource()->getAttribute('gemfind_diamond_cut');
                 if ($attribute->usesSource()) {
                      $cutvalue = $attribute->getSource()->getOptionId($diamondData['diamondData']['cut']);
                }    
            }

            $product = $this->product;
            if($product->getIdBySku($diamondData['diamondData']['diamondId'])) {
                $product->load($product->getIdBySku($diamondData['diamondData']['diamondId']));
                $product->setPrice(str_replace(',', '', $diamondData['diamondData']['fltPrice']));
                $product->setQuantityAndStockStatus(['qty' => 1, 'is_in_stock' => 1]);
                $product->save();
            } else {
                $productData = [
                'name' => $diamondName,
                'description' => $diamondName,
                'short_description' => $diamondData['diamondData']['subHeader'],
                'type_id'=>Type::TYPE_SIMPLE,
                'sku' =>$diamondData['diamondData']['diamondId'],
                'attribute_set_id' => $attributeSetId,
                'weight' => 1,
                'visibility' =>\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                'status' => true,
                'tax_class_id' => 2,
                'price' => str_replace(',', '', $diamondData['diamondData']['fltPrice']),
                'website_ids' => [$this->_storeManager->getWebsite()->getId()],
                'stock_data' =>  [
                                    'use_config_manage_stock' => 0,
                                    'manage_stock' => 0,
                                    'is_in_stock' => 1,
                                    'min_sale_qty' => 1,
                                    'max_sale_qty' => 1,
                                    'qty' => 1
                                ],
                'url_key'=>$diamondName.$diamondData['diamondData']['diamondId'],
                'gemfind_diamond_shape' => $shapevalue,
                'gemfind_diamond_cut' => $cutvalue,
                ];
                $product->setData($productData);
                if($diamondData['diamondData']['fancyColorMainBody']) { 
                  if(!$this->is_404($diamondData['diamondData']['colorDiamond'])){
                                   $imageurl = $diamondData['diamondData']['colorDiamond'];
                                 }
                } else { 
                  if(!$this->is_404($diamondData['diamondData']['image2'])){
                                   $imageurl = $diamondData['diamondData']['image2'];
                                 } 
                }
                 if (isset($imageurl)) {
                    $imageUrl = $imageurl;
                    if(!$this->is_404($imageUrl)){
                      if ($imageUrl) {
                          $imageType = substr(strrchr($imageUrl, "."), 1);

                          //$filename = md5($imageUrl . $diamondName) . '.' . $imageType;
                          $filename = $imageUrl . strtolower(str_replace(' ', '_', $diamondName)) . $diamondData['diamondData']['diamondId'] . '.' . $imageType;
                          $tmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp';
                          $this->file->checkAndCreateFolder($tmpDir);
                          $localImageUrl = $tmpDir . DIRECTORY_SEPARATOR . $filename;
                          file_put_contents($localImageUrl, file_get_contents(trim($imageUrl)));
                          $product->addImageToMediaGallery($localImageUrl, ['image', 'small_image', 'thumbnail'], false, false);
                      }
                    }
                  }
            }

            $product->save();
            $this->cart->addProduct($product, ['product'=>$product->getId(),'qty'=>1]);
            $this->cart->save();

            if (!$this->cart->getQuote()->getHasError()) {
                $message = __(
                    'You added %1 to your shopping cart.',
                    $product->getName()
                );
                $this->messageManager->addSuccessMessage($message);
            }

            $this->_redirect("checkout/cart/");
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

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
}
