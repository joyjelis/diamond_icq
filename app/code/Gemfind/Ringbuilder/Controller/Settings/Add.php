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

class Add extends Action
{

    const ATTRIBUTE_SET = 'Gemfind Ringbuilder';

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
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $optioninterface;


    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productrepo;

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
        CustomerCart $cart,
        \Magento\Catalog\Model\Product\OptionFactory $optioninterface,
        \Magento\Catalog\Api\ProductRepositoryInterface $productrepo
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->product = $product;
        $this->attributeSet = $attributeSet;
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->cart = $cart;
        $this->optioninterface = $optioninterface;
        $this->productrepo = $productrepo;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPostValue();
        if(isset($data['ringsizesettingonly'])){
          $ringsize = $data['ringsizesettingonly'];  
        } else {
          $ringsize = 7;
        }
        
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

            $ringData = $this->helper->getRingById($id);

            if (!count($ringData)) {
                $this->messageManager->addError(__('Something went wrong. Please try again later!'));
                return $resultRedirect;
            }

            $shapevalue = $metalTypevalue = $sideStoneQualityvalue = $centerStoneSizevalue = '';
            $optionsdata = array();

            $diamondName = $ringData['ringData']['settingName'];

            if(isset($ringData['ringData']['shape'])){
                $shapevalue = $ringData['ringData']['settingName'];
            }
                        
            if(isset($ringData['ringData']['metalType'])){
                $metalTypevalue = $ringData['ringData']['metalType'];
            }

            if(isset($ringData['ringData']['sideStoneQuality'][0])){
                $sideStoneQualityvalue = $ringData['ringData']['sideStoneQuality'][0];
            }

            if(isset($ringData['ringData']['centerStoneMinCarat'])){
                $centerStoneSizevalue = $ringData['ringData']['centerStoneMinCarat'];
            }

            $product = $this->product;
            if($product->getIdBySku("rb-".$ringData['ringData']['settingId'])) {
                $product->load($product->getIdBySku("rb-".$ringData['ringData']['settingId']));
                $product->setPrice(str_replace(',', '', $ringData['ringData']['cost']));
                $product->setQuantityAndStockStatus(['qty' => 1, 'is_in_stock' => 1]);
                $this->productrepo->save($product);
                foreach ($product->getOptions() as $o) {
                      if($o->getTitle() == 'Ring Size'){
                        $optionsdata[$o->getOptionId()] = ($ringsize)?$ringsize:'NA';
                      }
                      if($o->getTitle() == 'Metal Type'){
                        $optionsdata[$o->getOptionId()] = ($metalTypevalue)?$metalTypevalue:'NA';
                      }
                      if($o->getTitle() == 'Side Stone Quality'){
                        $optionsdata[$o->getOptionId()] = ($sideStoneQualityvalue)?$sideStoneQualityvalue:'NA';
                      }  
                      if($o->getTitle() == 'Center Stone Size'){
                        $optionsdata[$o->getOptionId()] = ($centerStoneSizevalue)?$centerStoneSizevalue:'NA';
                      }                                            
                }
                $this->cart->addProduct($product, ['product'=>$product->getId(),'qty'=>1,'options' => $optionsdata]);
                $this->cart->save();
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                }
                $this->_redirect("checkout/cart/");
            } else {
                if($metalTypevalue){
                  $metalType = strtolower(str_replace(' ', '-', $metalTypevalue)).'-metaltype-';
                } else {
                  $metalType = '';
                }
                $name = strtolower(str_replace(' ', '-', $ringData['ringData']['settingName']));
                $sku = '-sku-rb-'.str_replace(' ', '-', $ringData['ringData']['settingId']);
                $urlkey = $metalType.$name.$sku;
                $productData = [
                'name' => $diamondName,
                'description' => $diamondName,
                'short_description' => $ringData['ringData']['description'],
                'type_id'=>Type::TYPE_SIMPLE,
                'sku' => "rb-".$ringData['ringData']['settingId'],
                'attribute_set_id' => $attributeSetId,
                'weight' => 1,
                'visibility' =>\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                'status' => true,
                'tax_class_id' => 2,
                'price' => str_replace(',', '', $ringData['ringData']['cost']),
                'website_ids' => [$this->_storeManager->getWebsite()->getId()],
                'stock_data' =>  [
                                    'use_config_manage_stock' => 0,
                                    'manage_stock' => 0,
                                    'is_in_stock' => 1,
                                    'min_sale_qty' => 1,
                                    'max_sale_qty' => 1,
                                    'qty' => 1
                                ],
                'url_key'=>$urlkey,
                'gemfind_ring_shape' => $shapevalue,
                'gemfind_ring_metaltype' => $metalTypevalue,
                'gemfind_ring_sidestone' => $sideStoneQualityvalue,
                ];
                $product->setData($productData);
                if($ringData['ringData']['mainImageURL']) { 
                  if(!$this->is_404($ringData['ringData']['mainImageURL'])){
                        $imageurl = $ringData['ringData']['mainImageURL'];
                    } 
                }
                 if (isset($imageurl)) {
                    $imageUrl = $imageurl;
                    if(!$this->is_404($imageUrl)){
                      if ($imageUrl) {
                          $imageType = substr(strrchr($imageUrl, "."), 1);

                         // $filename = md5($imageUrl . $diamondName) . '.' . $imageType;
                          $filename = strtolower(str_replace(' ', '_', str_replace('/', '_', $diamondName))) . $ringData['ringData']['settingId'] . '.' . $imageType;
                          $tmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp';
                          $this->file->checkAndCreateFolder($tmpDir);
                          $localImageUrl = $tmpDir . DIRECTORY_SEPARATOR . $filename;
						  if(!is_dir($tmpDir)) {
								mkdir($tmpDir , 0777);
						  }
                          file_put_contents($localImageUrl, file_get_contents(trim($imageUrl)));
                          $product->addImageToMediaGallery($localImageUrl, ['image', 'small_image', 'thumbnail'], false, false);
                      }
                    }
                  }
                  $product->setHasOptions(1);
                  $product->save();
                  $options = array(
                          array(
                              "sort_order"    => 1,
                              "title"         => "Ring Size",
                              "price_type"    => "fixed",
                              "price"         => "",
                              "type"          => "field",
                              "is_require"    => 1
                          ),
                          array(
                              "sort_order"    => 2,
                              "title"         => "Metal Type",
                              "price_type"    => "fixed",
                              "price"         => "",
                              "type"          => "field",
                              "is_require"    => 1
                          ),
                          array(
                              "sort_order"    => 3,
                              "title"         => "Side Stone Quality",
                              "price_type"    => "fixed",
                              "price"         => "",
                              "type"          => "field",
                              "is_require"    => 1
                          ),
                          array(
                              "sort_order"    => 4,
                              "title"         => "Center Stone Size",
                              "price_type"    => "fixed",
                              "price"         => "",
                              "type"          => "field",
                              "is_require"    => 1
                          )
                      );
              foreach ($options as $arrayOption) {
                $option = $this->optioninterface->create()->setProductId($product->getId())
                  ->setStoreId($product->getStoreId())
                  ->addData($arrayOption);
                  $option->save();
                  $product->addOption($option);
               }
              $this->productrepo->save($product);
              $product->load($product->getId());
              foreach ($product->getOptions() as $o) {
                      if($o->getTitle() == 'Ring Size'){
                        $optionsdata[$o->getOptionId()] = ($ringsize)?$ringsize:'NA';
                      }
                      if($o->getTitle() == 'Metal Type'){
                        $optionsdata[$o->getOptionId()] = ($metalTypevalue)?$metalTypevalue:'NA';
                      }
                      if($o->getTitle() == 'Side Stone Quality'){
                        $optionsdata[$o->getOptionId()] = ($sideStoneQualityvalue)?$sideStoneQualityvalue:'NA';
                      }
                      if($o->getTitle() == 'Center Stone Size'){
                        $optionsdata[$o->getOptionId()] = ($centerStoneSizevalue)?$centerStoneSizevalue:'NA';
                      }                                            
                }
                $this->cart->addProduct($product, ['product'=>$product->getId(),'qty'=>1,'options' => $optionsdata]);
                $this->cart->save();

              if (!$this->cart->getQuote()->getHasError()) {
                  $message = __(
                      'You added %1 to your shopping cart.',
                      $product->getName()
                  );
                  $this->messageManager->addSuccessMessage($message);
              }
              $RetailerID = $this->helper->getUsername();
                $price = $ringData['ringData']['cost'];
                $posturl = str_replace(' ', '+', 'http://platform.jewelcloud.com/ActivityTracking.aspx?RetailerID='.$RetailerID.'&Type=Addtocart&URL=http://www.gemfind.net&ProductId='.$product->getId().'&Price='.$price);
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $posturl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_TIMEOUT, $this->helper->getApiTimeout());
                $responce = curl_exec($curl);
                $results = json_decode($responce);
                if (curl_errno($curl)) {
                }
                curl_close($curl);
              $this->_redirect("checkout/cart/");
              }
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
