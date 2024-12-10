<?php

namespace Customer\Sell\Helper;

use Customer\Sell\Model\Sell;
use Customer\Sell\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Address\Config;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\SessionFactory;
use Magento\Directory\Block\Data as DirectoryHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\RequestFactory;
use Magento\Framework\View\Element\BlockFactory;
use Magneto\Logger\Logger\Handler as CustomLog;

class Data extends AbstractHelper
{

    const LABDATA = [
        'lab', 'carat', 'color', 'clarity', 'cut', 'polish', 'symmetry', 'fluorescence', 'certificate_no',
    ];

    const PLACEHOLDER = "Placeholder.png";

    const TRADE_TYPE = [
        1 => "Trade for Cash",
        2 => "Trade for Bigger Diamond",
        3 => "Request Quote",
    ];

    protected $addresstemplate = "{{street}},<br> {{landmark}},<br> {{city}} {{pickup_country}} - {{postcode}}";

    protected $session;

    protected $selldata = [];

    protected $_limit = 10;

    protected $historycollection;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Customer\Sell\Model\SellFactory $sellFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Customer\Sell\Model\HistoryFactory $historyFactory,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        Config $addressConfig,
        Mapper $addressMapper,
        AddressRepositoryInterface $addressRepo,
        DirectoryHelper $directoryBlock,
        SessionFactory $customerSession,
        RequestFactory $requestFactory,
        CustomerExtractor $customerExtractor,
        AccountManagementInterface $customerAccountManagement,
        BlockFactory $blockFactory,
        CustomLog $logger,
        Session $session
    ) {
        $this->logger = $logger;
        $this->_addressConfig = $addressConfig;
        $this->_addressRepo = $addressRepo;
        $this->addressMapper = $addressMapper;
        $this->directoryBlock = $directoryBlock;
        $this->_customerSession = $customerSession;
        $this->priceCurrency = $priceCurrency;
        $this->moduleReader = $moduleReader;
        $this->customer = $customer;
        $this->_storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->_filesystem = $filesystem;
        $this->mathRandom = $mathRandom;
        $this->_eventManager = $eventManager;
        $this->sellFactory = $sellFactory;
        $this->historyFactory = $historyFactory;
        $this->requestFactory = $requestFactory;
        $this->customerExtractor = $customerExtractor;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->blockFactory = $blockFactory;
        $this->session = $session;
        parent::__construct($context);
    }

    public function getPlaceholderImage()
    {
        $viewDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'Customer_Sell'
        );
        return $viewDir . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . self::PLACEHOLDER;
    }

    public function IsValidUser($sellId, $customerid)
    {
        $sell = $this->sellFactory->create()->load($sellId);
        return $sell->getCustomerId() == $customerid ? true : false;
    }

    public function ProcessLabData($data)
    {
        $checkKey = self::LABDATA;
        /** set values as NA when any of below field is empty.**/
        array_walk($data, function (&$item, $key) use (&$checkKey) {
            if (in_array($key, $checkKey) && empty($item)) {
                $item = 'NA';
            }
        });

        return $data;
    }

    public function getAddress($sellId)
    {
        $sell = $this->sellFactory->create()->load($sellId);
        if ($sell) {
            $address = $this->addresstemplate;
            if ($sell->getStreet() && $sell->getLandmark() && $sell->getCity() && $sell->getPickupCountry() && $sell->getPostcode()) {
                $address = str_replace('{{street}}', $sell->getStreet(), $address);
                $address = str_replace('{{landmark}}', $sell->getLandmark(), $address);
                $address = str_replace('{{city}}', $sell->getCity(), $address);
                $address = str_replace('{{pickup_country}}', $sell->getPickupCountry(), $address);
                $address = str_replace('{{postcode}}', $sell->getPostcode(), $address);
                return $address;
            }
        }

        return false;
    }

    public function storeAddress($id, $data)
    {
        $sell = $this->sellFactory->create()->load($id);
        if ($sell) {
            if (isset($data['return_shpping']) && $data['return_shpping'] == 1) {
                unset($data['return_shpping']);
                unset($data['ajax']);
                foreach ($data as $key => $value) {
                    $sell->setData($key, $value);
                }

                $sell->setGenerateQuoteId(0);
                $sell->setQuoteNoUpdate(1);
                if ($sell->save()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function ProcessStatus($id, $data)
    {
        $sell = $this->sellFactory->create()->load($id);
        if ($sell) {
            if (isset($data['accept_offer'])) {
                if ($data['accept_offer'] == 1) {
                    $sell->setStatus(Sell::CUSTOMER_ACCEPTED_OFFER_STATUS);
                } elseif ($data['accept_offer'] == 0) {
                    $sell->setStatus(Sell::CUSTOMER_NOT_ACCEPTED_OFFER_STATUS);
                }
            }

            if (isset($data['trade_type']) && $data['trade_type'] == 1) {
                $sell->setStatus(Sell::CUSTOMER_REQUESTED_PICK_UP_STATUS);
                unset($data['ajax']);
                foreach ($data as $key => $value) {
                    $sell->setData($key, $value);
                }
            }

            if (isset($data['return_shpping']) && $data['return_shpping'] == 1) {
                $sell->setStatus(Sell::CUSTOMER_ASKED_FOR_RETURN_SHIPPING_PAYMENT_LINK_STATUS);
                if (isset($data['return_shpping'])) {
                    unset($data['return_shpping']);
                }

                if (isset($data['ajax'])) {
                    unset($data['ajax']);
                }
                foreach ($data as $key => $value) {
                    $sell->setData($key, $value);
                }
            }

            if (isset($data['offer_consignemnt'])) {
                if ($data['offer_consignemnt'] == 1) {
                    $sell->setStatus(Sell::CUSTOMER_ACCEPTED_CONSIGNMENT_STATUS);
                } elseif ($data['offer_consignemnt'] == 2) {
                    $sell->setStatus(Sell::CUSTOMER_NOT_ACCEPTED_CONSIGMENT_STATUS);
                }
            }

            if (isset($data['admin_accept_offer'])) {
                if ($data['admin_accept_offer'] == 1) {
                    $sell->setStatus(Sell::CUSTOMER_ACCEPTED_CONSIGNMENT_STATUS);
                } elseif ($data['admin_accept_offer'] == 0) {
                    $sell->setStatus(Sell::CUSTOMER_NOT_ACCEPTED_CONSIGMENT_STATUS);
                }
            }
        }

        $sell->setGenerateQuoteId(0);
        $sell->setQuoteNoUpdate(1);
        if ($sell->save()) {
            $sell = $this->sellFactory->create()->load($sell->getId());

            $data['status'] = $sell->getStatus();
            $this->ProcessHistory($data, $sell->getId());

            $this->_eventManager->dispatch(
                'sell_diamond_email',
                ['sell' => $sell]
            );

            return true;
        }

        return false;
    }

    public function SaveData($sellId, $data)
    {
        $sell = $this->sellFactory->create()->load($sellId);
        if ($sell) {
            $sell->setData($data);
            $sell->setGenerateQuoteId(0);
            $sell->setQuoteNoUpdate(1);
            $sell->save();
            $this->ProcessHistory($data, $sellId);
            $sell = $this->sellFactory->create()->load($sell->getId());

            $this->_eventManager->dispatch(
                'sell_diamond_email',
                ['sell' => $sell]
            );

            return true;
        }

        return false;
    }

    public function ProcessHistory($data, $sellId)
    {
        $history = $this->historyFactory->create();
        $history->setSellId($sellId);
        $history->setStatus($data['status']);
        if (isset($data['remarks']) && !empty($data['remarks'])) {
            $history->setRemarks($data['remarks']);
        }
        $history->save();

        return true;
    }

    public function getNextStep($status)
    {
        return $this->sellFactory->create()->getNextStep($status);
    }

    public function getItemStatus($status)
    {
        $allstatus = $this->sellFactory->create()->getStatusOptions();
        return isset($allstatus[$status]) ? $allstatus[$status] : "";
    }

    public function getHistoryCollection()
    {
        return $this->historycollection;
    }

    public function getHistory($sellId, $page = 40)
    {
        $history = $this->historyFactory->create()->getCollection()
            ->addFieldToFilter('sell_id', $sellId)
            ->setPageSize($this->_limit)
            ->setCurPage($page)
            ->setOrder('created_at', "DESC");

        $this->historycollection = $history;

        $history_format = [];
        if ($history) {
            foreach ($history->getData() as $key => $value) {
                $history_format[$key] = $value;
                $history_format[$key]['date'] = date('d-m-Y', strtotime($value['created_at']));
                $history_format[$key]['time'] = date('G:i:s T', strtotime($value['created_at']));
                $history_format[$key]['status'] = $this->getItemStatus($value['status']);
            }
        }

        return $history_format;
    }

    public function ProcessPrice($data)
    {
        $data['price'] = $this->getCurrencyWithFormat($data['price']);
        $data['offer_price'] = $this->getCurrencyWithFormat($data['offer_price']);
        return $data;
    }

    public function ProcessImages($data)
    {
        $imageList = [];
        $images = (isset($data['image'])) ? $data['image'] : [];
        if (is_array($images) && count($images)) {
            foreach ($images as $image) {
                if (isset($image['name'])) {
                    $imageList[] = $image['name'];
                }
            }
        }

        return $imageList;
    }

    public function getItemsHtml()
    {
        $block = $this->blockFactory->createBlock(\Customer\Sell\Block\Quote\Items::class);

        $page_size = array_combine(\Customer\Sell\Block\Quote\Items::PAGE_LIMIT, \Customer\Sell\Block\Quote\Items::PAGE_LIMIT);
        $pager = $this->blockFactory->createBlock(\Magento\Theme\Block\Html\Pager::class)->setAvailableLimit($page_size)->setShowPerPage(true)->setCollection($block->getCollection());

        $html = $block->toHtml();
        $pager = $pager->toHtml();
        return ['block' => $html, 'pager' => $pager];
    }

    public function ConvertCurrency($price)
    {
        $currentCurrency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $baseCurrency = $this->_storeManager->getStore()->getBaseCurrency()->getCode();
        if ($currentCurrency != $baseCurrency) {
            $price = round(filter_var($price, FILTER_SANITIZE_NUMBER_INT), 0);
            $rate = $this->currencyFactory->create()->load($baseCurrency)->getAnyRate($currentCurrency);
            $returnValue = $price * $rate;
            $price = $returnValue;
        }

        return $price;
    }

    public function getCurrencyWithFormat($price)
    {
        try {
            if (strpos($price, 'to') !== false) {
                $price = explode('to', $price);
                return $this->priceCurrency->format((int) $this->ConvertCurrency($price[0])) . ' to ' . $this->priceCurrency->format((int) $this->ConvertCurrency($price[1]));
            }

            if (is_numeric($price)) {
                return $this->priceCurrency->format((int) $this->ConvertCurrency($price));
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    public function SaveBase64images($data, $location, $savetotemp = 0)
    {
        if ($savetotemp == 1) {
            return $this->SaveToTemp($data);
        }

        if ($data) {
            $path = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT)
                ->getAbsolutePath($location);

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            if (!empty($data->data)) {
                $image_parts = explode(";base64,", $data->data);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = uniqid() . '.' . $image_type;
                $file = $path . DIRECTORY_SEPARATOR . $filename;
                if (file_put_contents($file, $image_base64)) {
                    return $filename;
                }
            }
        }
    }

    public function SaveToTemp($data)
    {
        $path = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT)
            ->getAbsolutePath('tmp/sellitems');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if (!empty($data->data)) {
            $image_parts = explode(";base64,", $data->data);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $filename = uniqid() . '.' . $image_type;
            $file = $path . DIRECTORY_SEPARATOR . $filename;
            if (file_put_contents($file, $image_base64)) {
                return $filename;
            }
        }
    }

    public function IsAppointmentBook($quote_id)
    {
        $sell = $this->sellFactory->create()->load($quote_id, 'quote');
        if (!empty($sell->getScheduleDate())) {
            return date_format(date_create($sell->getScheduleDate()), "d M, Y");
        } else {
            return false;
        }
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function createQuote($item, $quote_id)
    {
        $data = json_decode(base64_decode($item));

        // $this->logger->write(print_r($quote_id, true));
        // $this->logger->write(print_r($data, true));

        $storeId = $this->_storeManager->getStore()->getId();
        $imageslist = [];
        if (!empty($data->images)) {
            foreach ($data->images as $files) {
                $imageslist[] = $this->SaveBase64images($files, 'sell/dimages/', 0);
            }
        }

        $image = implode(',', $imageslist);
        if (!empty($quote_id)) {
            $sell = $this->sellFactory->create()->load($quote_id, 'quote');
        } else {
            $sell = $this->sellFactory->create();
        }

        $sell->setGenerateQuoteId(0);

        if ($data->step == "form") {
            $sell->setGenerateQuoteId(1);
            $customerid = $this->CreateCustomerAccount($data);
            $this->setCustomerPassword("");
            $sell->setCustomerId($customerid);
        }

        $sell->setImage($image);
        $sell->setMobile($data->mobile);
        $sell->setstore_id($storeId);

        if (isset($data->method)) {
            if (isset(self::TRADE_TYPE[$data->method])) {
                $sell->setJewelleryType(self::TRADE_TYPE[$data->method]);
            }
        }

        if (isset($data->fname) && isset($data->lname)) {
            $sell->setName($data->fname . ' ' . $data->lname);
        }

        if (isset($data->email)) {
            $sell->setEmail($data->email);
        }

        $sell->setImagetype($data->imagetype);

        if ($data->imagetype == "certificate_images") {
            $sell->setCertificate(1);
        }

        $sell->save();

        $sell = $this->sellFactory->create()->load($sell->getId());

        if ($data->step == "form") {
            $this->_eventManager->dispatch(
                'sell_diamond_email',
                ['sell' => $sell]
            );
        }

        return $sell->getData();
    }

    public function saveItems($item, $quote_id = null)
    {
        if (!empty($quote_id)) {
            $data = $this->createQuote($item, $quote_id);
        } else {
            $data = $this->createQuote($item, $quote_id);
        }
        return ['quote_id' => $data['quote'], 'date' => $data['created_at']];
    }

    public function CreateCustomerAccount($data)
    {
        if (isset($data->email)) {
            $customer = $this->customerExists($data->email);
            if ($customer) {
                return $customer->getId();
            } else {
                $customerData = [
                    'firstname' => $data->fname,
                    'lastname' => $data->lname,
                    'email' => $data->email,
                ];

                $password = $this->generatePassword(); //set null to auto-generate
                $this->setCustomerPassword($password);

                $request = $this->requestFactory->create();
                $request->setParams($customerData);

                try {
                    $customer = $this->customerExtractor->extract('customer_account_create', $request);
                    $customer = $this->customerAccountManagement->createAccount($customer, $password);
                    return $customer->getId();
                } catch (\Exception $e) {
                    //exception logic
                    die($e->getMessage());
                }

            }
        }
    }

    public function generatePassword($length = 10)
    {
        $chars = \Magento\Framework\Math\Random::CHARS_LOWERS
        . \Magento\Framework\Math\Random::CHARS_UPPERS
        . \Magento\Framework\Math\Random::CHARS_DIGITS;

        return $password = $this->mathRandom->getRandomString($length, $chars);
    }

    public function setCustomerPassword($password)
    {
        $this->session->setselldata($password);
        return true;
    }

    public function getCustomerPassword()
    {
        return $this->session->getselldata();
    }

    public function customerExists($email, $websiteId = null)
    {
        $customer = $this->customer;

        if (empty($websiteId)) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        }

        $customer->setWebsiteId($websiteId);

        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }

        return false;
    }

    public function getCountries($defValue = null, $name = 'country_id', $id = 'country', $title = 'Country')
    {
        $country = $this->directoryBlock->getCountryHtmlSelect($defValue, $name, $id, $title);
        return $country;
    }

    public function getRegion()
    {
        $region = $this->directoryBlock->getRegionHtmlSelect();
        return $region;
    }

    public function getImagepath($image)
    {
        $path = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT)
            ->getAbsolutePath();
        return $path . 'sell/dimages/' . $image;
    }

    public function getFirstImage($image)
    {
        $rootPath = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT);
        $path = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT)
            ->getAbsolutePath();

        if ($rootPath->isFile('sell/dimages/' . $image)) {
            return $path . 'sell/dimages/' . $image;
        }

        return $this->getPlaceholderImage();
    }

    public function getSellitem($sellId)
    {
        if (!$this->_customerSession->create()->getId()) {
            return false;
        }

        $sellItems = $this->sellFactory->create()->load($sellId);
        return $sellItems;
    }
}
