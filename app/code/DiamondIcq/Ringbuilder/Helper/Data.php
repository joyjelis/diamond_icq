<?php

namespace DiamondIcq\Ringbuilder\Helper;

class Data extends \Gemfind\Ringbuilder\Helper\Data
{
    public const XML_PATH_RAPNET_USERNAME = 'gemfindringbuilder/general/rapnet_username';
    public const XML_PATH_RAPNET_PASSWORD = 'gemfindringbuilder/general/rapnet_password';

    public const RINGBUILDER_CURRENCY = 'USD';

    public const GIRDLE_VALUES = [
        'XTN' => 'Extr Thin',
        'VTN' => 'Very Thin',
        'TN' => 'Thin',
        'STN' => 'Slightly Thin',
        'M' => 'Medium',
        'STK' => 'Slightly Thick',
        'TK' => 'Thick',
        'VTK' => 'Very Thick',
        'XTK' => 'Extra Thick',
    ];

    public const CULET_VALUES = [
        'N' => 'None',
        'VS' => 'Very Small',
        'S' => 'Small',
        'M' => 'Medium',
        'L' => 'Large',
    ];

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->encryptor = $encryptor;
        $this->priceCurrency = $priceCurrency;
        $this->assetRepo = $assetRepo;
        $this->attributeRepository = $attributeRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->configurable = $configurable;
        $this->attribute = $attribute;
        $this->attributeSetCollection = $attributeSetCollection;
        $this->productRepository = $productRepository;
        parent::__construct(
            $context,
            $logger,
            $cookieManager,
            $storeManager,
            $orderCollectionFactory,
            $product,
            $attributeSetRepository
        );
    }

    /**
     * return Rapnet API Username
     */
    public function getRapnetUsername()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RAPNET_USERNAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * return Rapnet API Password
     */
    public function getRapnetPassword()
    {
        $password = $this->scopeConfig->getValue(
            self::XML_PATH_RAPNET_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $this->encryptor->decrypt($password);
    }

    /**
     *
     * @param string $attributeSetName
     * @return int attributeSetId
     */
    public function getAttributeSetId($attributeSetName)
    {
        $attributeSetCollection = $this->attributeSetCollection->create()
            ->addFieldToSelect('attribute_set_id')
            ->addFieldToFilter('attribute_set_name', $attributeSetName)
            ->getFirstItem()
            ->toArray();

        if (!empty($attributeSetCollection['attribute_set_id'])) {
            return (int)$attributeSetCollection['attribute_set_id'];
        }
        return 0;
    }

    // getAttribute
    public function getAttributeCodeById($id)
    {
        $attributeModel = $this->attribute->load($id);
        $attributeCode = $attributeModel->getAttributeCode();
        return $attributeCode;
    }

    // getAttributeOption
    public function getAttributeOptionByCode($code)
    {
        $attribute = $this->attributeRepository->get($code);
        $options = $attribute->getOptions();
        return $options;
    }

    public function getImageUrl($file_path)
    {
        return $this->assetRepo->getUrl("DiamondIcq_Ringbuilder::images/{$file_path}");
    }

    public function getProductImageUrl($image_path)
    {
        $_base_url = $this->_storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return "{$_base_url}catalog/product" . $image_path;
    }

    public function getRingVideoUrl($product)
    {
        $videoUrl = false;
        if ($product->getTypeId() == 'configurable') {
            $videoUrl = $product->getAttributeText('videourl');
        } elseif ($product->getTypeId() == 'simple') {
            $parentProduct = $this->getRingParentProduct($product);
            if (!empty($parentProduct)) {
                $videoUrl = $parentProduct->getAttributeText('videourl');
            }
        }

        return (empty($videoUrl)) ? '' : $videoUrl;
    }

    public function getCertificateLabIconUrl($lab)
    {
        if (empty($lab)) {
            return "";
        }
        $lab = strtolower($lab);
        return $this->getImageUrl("labs/{$lab}.jpeg");
    }

    public function getCertificateLabUrl($lab, $certificate_no)
    {
        if (empty($certificate_no)) {
            return "";
        }
        $url = "";
        $lab = strtolower($lab);
        if ($lab == 'gia') {
            $url = "https://www.gia.edu/report-check?reportno={$certificate_no}";
        } elseif ($lab == 'igi') {
            $url = "https://www.igi.org/reports/verify-your-report?r={$certificate_no}";
        } elseif ($lab == 'hrd') {
            $url = "https://my.hrdantwerp.com/?record_number={$certificate_no}";
        } elseif ($lab == 'ags') {
            $url = "https://agslab.com/ym-vdgr/en-us/login?id={$certificate_no}";
        }
        return $url;
    }

    public function getGirdleText($value)
    {
        if (empty(self::GIRDLE_VALUES[$value])) {
            return $value;
        }
        return self::GIRDLE_VALUES[$value];
    }

    public function getCuletText($value)
    {
        if (empty(self::CULET_VALUES[$value])) {
            return $value;
        }
        return self::CULET_VALUES[$value];
    }

    /**
     * override Gemfind Ringbuilder getDiamondByIdtype()
     */
    public function getDiamondByIdtype($id, $type = null)
    {
        return $this->getDiamondById($id);
    }

    public function convertPrice($amount)
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $rate = $this->priceCurrency->convert($amount, $store, self::RINGBUILDER_CURRENCY);
        return $rate;
    }

    public function diamondPriceRange($diamond_type)
    {
        $table_name = $this->resource->getTableName('rapnet_diamond');
        $select_max = $this->connection->select()
            ->from($table_name, 'total_sales_price')
            ->where('status > 0')
            ->where('diamond_type = ?', $diamond_type)
            ->order("total_sales_price DESC");
        $max_price = $this->connection->fetchOne($select_max);

        $select_min = $this->connection->select()
            ->from($table_name, 'total_sales_price')
            ->where('status > 0')
            ->where('diamond_type = ?', $diamond_type)
            ->order("total_sales_price ASC");
        $min_price = $this->connection->fetchOne($select_min);
        return (object)['min' => $min_price, 'max' => $max_price];
    }

    public function getStyleSetting()
    {
        $settings = [
            'settings' => [
                'hoverEffect' => [
                    (object)["color1" => "#000022", "color2" => "#000022"],
                ],
                'columnHeaderAccent' => [
                    (object)["color1" => "#000000", "color2" => "#000000"],
                ],
                'linkColor' => [
                    (object)["color1" => "#828282", "color2" => "#828282"],
                ],
                'callToActionButton' => [
                    (object)["color1" => "#000022", "color2" => "#000022"],
                ]
            ]
        ];
        return $settings;
    }

    public function getStyleSettingRB()
    {
        return $this->getStyleSetting();
    }

    public function getActiveNavigation()
    {
        $response = [
            'navigation' => [
                'navStandard' => 'Mined',
                'navLabGrown' => null,
                'navFancyColored' => 'Fancy Color',
                'navCompare' => 'Compare',
            ],
            'total' => 4,
        ];
        return $response;
    }

    public function getJCOptiondata()
    {
        $data = [
            'internalUseLink' => false,
            'scheduleViewing' => true,
            'show_In_House_Diamonds_First' => false,
            'show_Advance_options_as_Default_in_Diamond_Search' => false,
            'show_Certificate_in_Diamond_Search' => true,
            'show_Request_Certificate' => true,
            'drop_A_Hint' => true,
            'email_A_Friend' => true,
            'show_Diamond_Prices' => true,
            'markup_Your_Own_Inventory' => false,
            'show_Pinterest_Share' => true,
            'show_Twitter_Share' => true,
            'show_Facebook_Share' => true,
            'show_Facebook_Like' => true,
            'show_AddtoCart_Buttom' => false,
        ];
        return ['data' => $data];
    }

    public function convertDiamondData($diamond)
    {
        if (is_array($diamond)) {
            $diamond = json_decode(json_encode($diamond));
        }
        // fix to shape value ex: "Cushion Modified" to "Cushion"
        $_shape = explode(" ", $diamond->shape);
        $diamond->shape = array_shift($_shape);
        $_shape = strtolower($diamond->shape);

        $diamondImage = $this->getImageUrl("diamonds/{$_shape}_Large.jpeg");
        $colorDiamondImage = null;
        if ($diamond->diamond_type == "Fancy") {
            $_color = $diamond->fancy_color_dominant_color;
            $colorDiamondImage = $this->getImageUrl("diamonds/{$_shape}-{$_color}_large.jpeg");
            $diamondImage = $colorDiamondImage;
        }

        $data = [
            "\$id" => "1",
            "diamondId" => $diamond->diamond_id,
            "mainHeader" => "{$diamond->size} Carat {$diamond->shape} Diamond",
            "subHeader" => "This {$diamond->cut} cut, {$diamond->color} color, {$diamond->clarity} " .
                "clarity diamond comes accompanied by a diamond grading report from {$diamond->lab}",
            "stockNumber" => $diamond->diamond_id,
            "price" => $diamond->total_sales_price,
            "caratWeight" => $diamond->size,
            "cut" => $diamond->cut,
            "color" => ($diamond->diamond_type == 'Fancy') ?
                "{$diamond->fancy_color_intensity}{$diamond->fancy_color_dominant_color}" : $diamond->color,
            "colorID" => "",
            "clarity" => "{$diamond->clarity}",
            "clarityID" => "",
            "cutGrade" => null,
            "cutGradeID" => "",
            "depth" => "{$diamond->depth_percent}",
            "table" => "{$diamond->table_percent}7",
            "polish" => "{$diamond->polish}",
            "symmetry" => "{$diamond->symmetry}",
            "gridle" => $this->getGirdleText($diamond->girdle_max),
            "culet" => $this->getCuletText($diamond->culet_size),
            "fluorescence" => "{$diamond->fluor_intensity}",
            "measurement" => "{$diamond->meas_length}X{$diamond->meas_width}X{$diamond->meas_depth}",
            "contactNo" => "+91 9537491847",
            "contactEmail" => "pritesh@diamondicq.com",
            "image1" => "https://apps.gemfind.net/dealerid_display/drawshape.aspx?" .
            "shape={$_shape}&measurements={$diamond->meas_length}X{$diamond->meas_width}X{$diamond->meas_depth}" .
            "&tablemeasure={$diamond->table_percent}&depth={$diamond->depth_percent}" .
            "&girdle={$diamond->girdle_condition}&culet={$diamond->culet_condition}",
            "image2" => $diamondImage,
            "colorDiamond" => $colorDiamondImage,
            "videoFileName" => (empty($diamond->video_url)) ? "" : $diamond->video_url,
            "certificate" => $diamond->lab,
            "price1" => "{$diamond->total_sales_price}",
            "price2" => "",
            "lotNumber" => "{$diamond->diamond_id}",
            "additionalImage" => null,
            "fltPrice" => "{$diamond->total_sales_price}",
            "txtInhouse" => false,
            "shape" => $diamond->shape,
            "providerImageUrl" => "http://gemfind.net/dealerid_display/dealerimages/",
            "certificateNo" => $diamond->cert_num,
            "certificateUrl" => $this->getCertificateLabUrl($diamond->lab, $diamond->cert_num),
            "certificateIconUrl" => $this->getCertificateLabIconUrl($diamond->lab),
            "dealerId" => "4098",
            "stoneCarat" => "{$diamond->size}",
            "origin" => "",
            "skun" => "{$diamond->diamond_id}",
            "isFavorite" => false,
            "ratio" => "",
            "costPerCarat" => "",
            "vendorName" => "Pritesh",
            "vendorID" => "4098",
            "vendorEmail" => "pritesh@diamondicq.com",
            "vendorContactNo" => "+91 9537491847",
            "vendorFax" => "",
            "vendorAddress" => "1105-6, Winfield Commercial Building, 6~8,Tsim Sha Tsui, Hong Kong",
            "vendorStockNo" => "{$diamond->diamond_id}",
            "sOrigin" => "",
            "wholeSalePrice" => "",
            "fancyColorMainBody" => "{$diamond->fancy_color_dominant_color}",
            "fancyColorIntensity" => "{$diamond->fancy_color_intensity}",
            "fancyColorOvertone" => "{$diamond->fancy_color_overtone}",
            "fancyColorSecondaryColor" => "{$diamond->fancy_color_secondary_color}",
            "currencyFrom" => "USD",
            "currencySymbol" => "$",
            "retailerStockNo" => "",
            "retailerInfo" => [
                "\$id" => "2",
                "retailerName" => "Pritesh",
                "retailerCompany" => "DIAMOND ICQ LIMITED",
                "retailerCity" => "Hongkong",
                "retailerState" => "Hongkong",
                "retailerContactNo" => "+91 9537491847",
                "retailerEmail" => "pritesh@diamondicq.com",
                "retailerLotNo" => "{$diamond->diamond_id}",
                "retailerStockNo" => "{$diamond->diamond_id}",
                "wholesalePrice" => "{$diamond->total_sales_price}",
                "thirdParty" => "",
                "diamondID" => null,
                "sellerName" => null,
                "sellerAddress" => null,
                "retailerID" => "4098",
                "retailerFax" => "",
                "retailerAddress" => "1105-6, Winfield Commercial Building, 6~8,Tsim Sha Tsui, Hong Kong",
                "addressList" => [
                    [
                        "\$id" => "3",
                        "locationID" => 2021,
                        "locationName" => "Hong Kong",
                        "address" => "1105-6, Winfield Commercial Building, 6~8,Tsim Sha Tsui, Hong Kong",
                        "email" => "pritesh@diamondicq.com",
                        "phone" => "+91 9537491847",
                        "city" => "Hongkong",
                        "state" => "Hongkong",
                        "zipCode" => "00000",
                        "country" => "Hong Kong"
                    ]
                ],
                "timingList" => [
                    [
                        "\$id" => "4",
                        "locationID" => 2021,
                        "sundayStart" => "11 AM",
                        "sundayEnd" => "7 PM",
                        "mondayStart" => "10:30 AM",
                        "mondayEnd" => "7 PM",
                        "tuesdayStart" => "10:30 AM",
                        "tuesdayEnd" => "7 PM",
                        "wednesdayStart" => "10:30 AM",
                        "wednesdayEnd" => "7 PM",
                        "thursdayStart" => "10:30 AM",
                        "thursdayEnd" => "7 PM",
                        "fridayStart" => "10:30 AM",
                        "fridayEnd" => "7 PM",
                        "saturdayStart" => "10:30 AM",
                        "saturdayEnd" => "7 PM",
                        "storeClosedMon" => "",
                        "storeClosedTue" => "",
                        "storeClosedWed" => "",
                        "storeClosedThu" => "",
                        "storeClosedFri" => "",
                        "storeClosedSat" => "",
                        "storeClosedSun" => ""
                    ]
                ]
            ],
            "internalUselink" => "",
            "girdleThin" => "",
            "girdleThick" => "",
            "country" => null,
            "diamondDeatilAdditionalInfo" => [
                "\$id" => "5",
                "shape" => $diamond->shape,
            ],
            "showPrice" => true,
            "isLabCreated" => false,
            "dsEcommerce" => true,
            "defaultDiamondImage" => $diamondImage,
        ];

        return $data;
    }

    public function convertDiamonds($data)
    {
        $diamonds = [];
        if (!empty($data)) {
            foreach ($data as $key_id => $diamond) {
                $_shape = explode(" ", $diamond->shape);
                $diamond->shape = array_shift($_shape);
                $_shape = strtolower($diamond->shape);

                $diamondImage = $this->getImageUrl("diamonds/{$_shape}_Large.jpeg");
                $colorDiamondImage = null;
                if ($diamond->diamond_type == "Fancy") {
                    $_color = $diamond->fancy_color_dominant_color;
                    $colorDiamondImage = $this->getImageUrl("diamonds/{$_shape}-{$_color}_large.jpeg");
                    $diamondImage = $colorDiamondImage;
                }

                $diamonds[] = [
                    "\$id" => (string)($key_id + 1),
                    "diamondId" => $diamond->diamond_id,
                    "diamondImage" => $diamondImage,
                    "pairDiamondId" => 0,
                    "sku" => $diamond->diamond_id,
                    "shape" => $diamond->shape,
                    "carat" => $diamond->size,
                    "fancyColorMainBody" => "{$diamond->fancy_color_dominant_color}",
                    "fancyColorIntensity" => "{$diamond->fancy_color_intensity}",
                    "fancyColorOvertone" => "{$diamond->fancy_color_overtone}",
                    "fancyColorSecondaryColor" => "{$diamond->fancy_color_secondary_color}",
                    "color" => ($diamond->diamond_type == 'Fancy') ?
                        "{$diamond->fancy_color_intensity}{$diamond->fancy_color_dominant_color}" : $diamond->color,
                    "clarity" => $diamond->clarity,
                    "cut" => $diamond->cut,
                    "inhouse" => "",
                    "depth" => $diamond->depth_percent,
                    "table" => $diamond->table_percent,
                    "polish" => $diamond->polish,
                    "measurement" => "{$diamond->meas_length}X{$diamond->meas_width}X{$diamond->meas_depth}",
                    "cert" => $diamond->lab,
                    "certificateUrl" => $this->getCertificateLabUrl($diamond->lab, $diamond->cert_num),
                    "price" => $diamond->total_sales_price,
                    "gridle" => $this->getGirdleText($diamond->girdle_max),
                    "culet" => $this->getCuletText($diamond->culet_size),
                    "symmetry" => $diamond->symmetry,
                    "fluorescence" => $diamond->fluor_intensity,
                    "fltCrown" => "",
                    "txtPavillion" => "",
                    "intClarityPriority" => "",
                    "intColorPriority" => "",
                    "certlink" => "",
                    "dealerID" => "4098",
                    "realPrice" => null,
                    "dealerInventoryNo" => $diamond->diamond_id,
                    "gfLinkID" => "4098",
                    "stones" => "0",
                    "comments" => "",
                    "intOptimize" => 1,
                    "sr_id" => 0,
                    "intTotalRecords" => 0,
                    "detailLinkText" => "View",
                    "detailLinkURL" => "",
                    "queryStringValues" => "",
                    "txtCutGrade" => "",
                    "isCompared" => "0",
                    "certificateNo" => $diamond->cert_num,
                    "fltCaratPrice" => "",
                    "ratio" => 0.0,
                    "pairID" => null,
                    "txtInhouse" => false,
                    "origin" => "",
                    "detailPageURL" => "",
                    "hasVideo" => true,
                    "videoFileName" => (empty($diamond->video_url)) ? "" : $diamond->video_url,
                    "imageFileName" => null,
                    "isInternalonly" => null,
                    "inventoryRegion" => "",
                    "enhancements" => "",
                    "price1" => $diamond->total_sales_price,
                    "price2" => "",
                    "lotNumber" => "",
                    "additionalImage" => null,
                    "fltPrice" => $diamond->total_sales_price,
                    "fav_id" => null,
                    "isFavorite" => false,
                    "biggerDiamondimage" => $diamondImage,
                    "colorDiamondImage" => $colorDiamondImage,
                    "currencyFrom" => $diamond->currency_code,
                    "currencySymbol" => $diamond->currency_symbol,
                    "isLabCreated" => false,
                    "girdleThin" => "",
                    "girdleThick" => "",
                    "country" => null,
                    "diamondListAdditionalInfo" => [
                        "\$id" => "3",
                        "tempVariable1" => "{$diamond->shape}"
                    ],
                    "showPrice" => true,
                    "cutGradeID" => "",
                    "dsEcommerce" => false
                ];
            }
        }
        return json_decode(json_encode($diamonds));
    }

    public function getRingConfigurableProducts($product)
    {
        $productsData = [];
        $parentProduct = $this->getRingParentProduct($product);
        if (!empty($parentProduct)) {
            $configProducts = $parentProduct->getTypeInstance()->getUsedProducts($parentProduct);
            foreach ($configProducts as $_cp) {
                $cProduct = $this->getProductById($_cp->getId());
                $productsData[] = (object)[
                    "gfInventoryId" => $cProduct->getSku(),
                    "metalType" => $cProduct->getAttributeText('metaltype'),
                    "sideStoneQuality" => $cProduct->getAttributeText('gemstonequality'),
                    "centerStoneSize" => $cProduct->getAttributeText('gemstonesize'),
                ];
            }
        }
        return $productsData;
    }

    public function getRingParentProduct($product)
    {
        $collection = $this->productCollectionFactory->create();

        // filter attribute set "Ringbuilder"
        $attributeSetId = $this->getAttributeSetId('Ringbuilder');
        $collection->addAttributeToFilter('attribute_set_id', $attributeSetId);

        // add join to group configurable products
        $collection->getSelect()->join(
            ['link_table' => 'catalog_product_super_link'],
            'link_table.product_id = e.entity_id',
            ['product_id','parent_id']
        );
        $collection->addAttributeToFilter('entity_id', $product->getId());
        $items = $collection->getItems();
        if (!empty($items)) {
            foreach ($items as $item) {
                return $this->getProductById($item->getParentId());
            }
        }
        return $product;
    }

    /**
     * override Gemfind Ringbuilder getDiamondById()
     */
    public function getDiamondById($id)
    {
        $table_name = $this->resource->getTableName('rapnet_diamond');
        $select = $this->connection->select()
            ->from($table_name, '*')
            ->where('diamond_id = :diamond_id');

        $bind = [':diamond_id' => (string)$id];
        $diamond = $this->connection->fetchRow($select, $bind);

        if (!empty($diamond['diamond_id'])) {
            $diamondData = $this->convertDiamondData($diamond);
            $returnData = ['diamondData' => (array)json_decode(json_encode($diamondData))];
        } else {
            $returnData = ['diamondData' => []];
        }

        return $returnData;
    }

    public function getProductById($id)
    {
        return $this->productRepository->getById($id);
    }

    public function getProductBySku($sku)
    {
        return $this->productRepository->get($sku);
    }

    /**
     * override Gemfind Ringbuilder sendRequest()
     */
    public function sendRequest($requestParam)
    {
        $page_size = 20;
        $offset = 0;
        $sort_by = 'diamond_id';
        $sort_direction = 'ASC';

        if (!empty($requestParam['page_size'])) {
            $page_size = (int)$requestParam['page_size'];
        }

        if (!empty($requestParam['page_number'])) {
            $page_number = (int)$requestParam['page_number'];
            $offset = ($page_number - 1) * $page_size;
        }

        if (!empty($requestParam['sort_by'])) {
            $sort_by = $requestParam['sort_by'];
            if ($sort_by == 'FltPrice') {
                $sort_by = "total_sales_price"; // Price
            } elseif ($sort_by == 'Cut') {
                $sort_by = "shape"; // Shape
            } elseif ($sort_by == 'Size') {
                $sort_by = "size"; // Size
            } elseif ($sort_by == 'Color') {
                $sort_by = "color"; // Color
            } elseif ($sort_by == 'ClarityID') {
                $sort_by = "clarity"; // Clarity
            } elseif ($sort_by == 'CutGrade') {
                $sort_by = "cut"; // Cut
            } elseif ($sort_by == 'Certificate') {
                $sort_by = "lab"; // Lab
            } elseif ($sort_by == 'Depth') {
                $sort_by = "depth_percent";
            } elseif ($sort_by == 'TableMeasure') {
                $sort_by = "table_percent";
            } elseif ($sort_by == 'Polish') {
                $sort_by = "polish";
            } elseif ($sort_by == 'Symmetry') {
                $sort_by = "symmetry";
            } elseif ($sort_by == 'Measurements') {
                $sort_by = "meas_length";
            } else {
                $sort_by = "size";
            }
        }

        if (!empty($requestParam['sort_direction'])) {
            $sort_direction = $requestParam['sort_direction'];
        }

        $table_name = $this->resource->getTableName('rapnet_diamond');
        $select = $this->connection->select()
            ->from($table_name, '*')
            ->order("{$sort_by} {$sort_direction}")
            ->limit($page_size, $offset);

        if ($sort_by == 'shape') {
            $select->order("size ASC");
        } elseif ($sort_by == 'size') {
            $select->order("shape ASC");
        } else {
            $select->order("size ASC");
            $select->order("shape ASC");
        }

        $select_count = $this->connection->select()
            ->from($table_name, 'count(*)');

        $where_filters = [
            "_status" => "status > 0",
            "shapes" => "shape IN (?)",
            "size_from" => "size >= ?",
            "size_to" => "size <= ?",
            "price_from" => "total_sales_price >= ?",
            "price_to" => "total_sales_price <= ?",
            "depth_percent_from" => "depth_percent >= ?",
            "depth_percent_to" => "depth_percent <= ?",
            "diamond_table_from" => "table_percent >= ?",
            "diamond_table_to" => "table_percent <= ?",
            "color" => "color IN (?)",
            "clarity" => "clarity IN (?)",
            "cut" => "cut IN (?)",
            "symmetry" => "symmetry IN (?)",
            "polish" => "polish IN (?)",
            "fluorescence_intensities" => "fluor_intensity IN (?)",
            "labs" => "lab IN (?)",
            "FancyColor" => "fancy_color_dominant_color IN (?)",
            "intIntensity" => "fancy_color_intensity IN (?)",
            "Filtermode" => "diamond_type IN (?)",
        ];

        $filter_map_values = [
            "cut" => [
                "1" => "Poor",
                "2" => "Excellent",
                "3" => "Very Good",
                "4" => "Good",
                "5" => "Fair",
            ],
            "color" => [
                "68" => "D",
                "69" => "E",
                "70" => "F",
                "71" => "G",
                "72" => "H",
                "73" => "I",
                "74" => "J",
                "75" => "K",
                "76" => "L",
                "77" => "M",
                "78" => "N",
                "79" => "O",
                "80" => "P",
            ],
            "clarity" => [
                "1" => "FL",
                "2" => "IF",
                "3" => "VVS1",
                "4" => "VVS2",
                "5" => "VS1",
                "6" => "VS2",
                "7" => "SI1",
                "8" => "SI2",
                "9" => "SI3",
                "10" => "I1",
                "11" => "I2",
                "12" => "I3",
            ],
            "polish" => [
                "1" => "Excellent",
                "2" => "Very Good",
                "3" => "Good",
                "4" => "Fair",
                "5" => "Poor",
            ],
            "symmetry" => [
                "1" => "Excellent",
                "2" => "Very Good",
                "3" => "Good",
                "4" => "Fair",
                "5" => "Poor",
            ],
            "fluorescence_intensities" => [
                "1" => "None",
                "2" => "Faint",
                "3" => "Medium",
                "4" => "Strong",
                "5" => "Very Strong",
            ],
            "Filtermode" => [
                "navstandard" => "White",
                "navfancycolored" => "Fancy",
            ]
        ];


        if (!$requestParam['did']) {
            foreach ($where_filters as $filter_key => $filter_sql) {
                $value = null;

                if (!empty($requestParam[$filter_key])) {
                    $value = $requestParam[$filter_key];
                    $_in_cond = stripos($filter_sql, " IN (?)");
                    if ($_in_cond !== false && $_in_cond > 0) {
                        $value = explode(",", $value);
                        $map_values = [];
                        if (!empty($filter_map_values[$filter_key])) {
                            $map_values = $filter_map_values[$filter_key];
                        }
                        foreach ($value as $idx => $val) {
                            if (!empty($map_values[$val])) {
                                $value[$idx] = $map_values[$val];
                            }
                        }
                    }
                    $select->where($filter_sql, $value);
                    $select_count->where($filter_sql, $value);
                } elseif (strpos($filter_key, '_') === 0) {
                    $select->where($filter_sql);
                    $select_count->where($filter_sql);
                }
            }
        } else {
            $filter_sql = 'diamond_id like ?';
            $value = '%'.$requestParam['did'].'%';
            $select->where($filter_sql, $value);
            $select_count->where($filter_sql, $value);
        }

        $dataset = $this->connection->fetchAll($select);
        $dataset = json_decode(json_encode($dataset));

        $count = $this->connection->fetchOne($select_count);

        $results = $this->convertDiamonds($dataset);

        if ($results && count($results) > 0) {
            $returnData = ['diamonds' => $results, 'total' => $count];
        } else {
            $returnData = ['diamonds' => [], 'total' => 0];
        }

        return $returnData;
    }

    /**
     * @return mixed
     */
    public function getRingFilters()
    {
        // ring collection options
        $options = $this->getAttributeOptionByCode('ringcollection');
        $results = [];
        $results['collections'] = [];
        foreach ($options as $option) {
            $optionData = (object)$option->getData();
            if (!empty($optionData->value)) {
                $image_file = 'ring-collections/' . strtolower($optionData->label) . '.jpeg';
                $results['collections'][] = (object)[
                    'collectionName' => $optionData->label,
                    'collectionImage' => $this->getImageUrl($image_file),
                    'isActive' => 1,
                ];
            }
        }

        // metal type options
        $options = $this->getAttributeOptionByCode('metaltype');
        $results['metalType'] = [];
        foreach ($options as $option) {
            $optionData = (object)$option->getData();
            if (!empty($optionData->value)) {
                $results['metalType'][] = (object)[
                    'metalType' => $optionData->label,
                    'isActive' => 1,
                ];
            }
        }

        $minPrice = 0;
        $maxPrice = 27000;
        $results['priceRange'] = [
            (object)['minPrice' => $minPrice, 'maxPrice' => $maxPrice],
        ];

        // gemstone shape options
        $options = $this->getAttributeOptionByCode('gemstoneshape');
        $results['shapes'] = [];
        foreach ($options as $option) {
            $optionData = (object)$option->getData();
            if (!empty($optionData->value)) {
                $image_file = 'gemstone-shapes/' . strtolower($optionData->label) . '.jpeg';
                $results['shapes'][] = (object)[
                    'shapeName' => $optionData->label,
                    'shapeImage' => $this->getImageUrl($image_file),
                    'isActive' => 1,
                ];
            }
        }

        return $results;
    }

    public function getRingById($id)
    {
        $product = $this->getProductBySku($id);

        $returnData = ['ringData' => []];
        if (!empty($product->getId())) {
            $productimages = $product->getMediaGalleryImages();
            $media_base_url = $this->_storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $imageUrl =  "{$media_base_url}catalog/product" . $product->getImage();
            $extraImage = [];
            foreach ($productimages as $productimage) {
                if ($product->getImage() == $productimage['file']) {
                    $imageUrl = $productimage['url'];
                    continue;
                }
                $extraImage[] = $productimage['url'];
            }
            $addressList = [
                (object)[
                    "\$id" => "35",
                    "locationID" => 0,
                    "locationName" => "Hong Kong",
                    "address" => "1105-6, Winfield Commercial Building, 6~8,Tsim Sha Tsui, Hong Kong",
                    "email" => "pritesh@diamondicq.com",
                    "phone" => "+91 9537491847",
                    "city" => "Hongkong",
                    "state" => "Hongkong",
                    "zipCode" => "00000",
                    "country" => "Hong Kong"
                ]
            ];
            $timingList = [
                (object)[
                    "\$id" => "36",
                    "locationID" => 0,
                    "sundayStart" => "11 AM",
                    "sundayEnd" => "7 PM",
                    "mondayStart" => "10:30 AM",
                    "mondayEnd" => "7 PM",
                    "tuesdayStart" => "10:30 AM",
                    "tuesdayEnd" => "7 PM",
                    "wednesdayStart" => "10:30 AM",
                    "wednesdayEnd" => "7 PM",
                    "thursdayStart" => "10:30 AM",
                    "thursdayEnd" => "7 PM",
                    "fridayStart" => "10:30 AM",
                    "fridayEnd" => "7 PM",
                    "saturdayStart" => "10:30 AM",
                    "saturdayEnd" => "7 PM",
                    "storeClosedMon" => "",
                    "storeClosedTue" => "",
                    "storeClosedWed" => "",
                    "storeClosedThu" => "",
                    "storeClosedFri" => "",
                    "storeClosedSat" => "",
                    "storeClosedSun" => ""
                ]
            ];

            $gemstonesize = $product->getAttributeText('gemstonesize');
            $centerStoneMinCarat = preg_replace('/^(\d+\.\d+)ct.*/', '$1', $gemstonesize);
            $centerStoneMaxCarat = preg_replace('/.*(\d+\.\d+)ct$/', '$1', $gemstonesize);
            $diamondSideDetail = [
                (object) [
                    'noOfDiamonds' => $product->getData('diamond_qty_side1'),
                    'diamondCut' => $product->getAttributeText('diamond_average_cut_side1'),
                    'minimumCaratWeight' => $product->getData('diamond_minumum_carat_total_weight_side1'),
                    'minimumColor' => $product->getAttributeText('diamond_average_color_side1'),
                    'minimumClarity' => $product->getAttributeText('diamond_average_clarity_side1'),
                    'settingType' => $product->getAttributeText('diamond_setting_type_side1'),
                    'diamondShape' => $product->getAttributeText('diamond_shape_side1'),
                    'diamondQuality' => $product->getAttributeText('diamond_quality_side1'),
                ],
                (object) [
                    'noOfDiamonds' => $product->getData('diamond_qty_side2'),
                    'diamondCut' => $product->getAttributeText('diamond_average_cut_side2'),
                    'minimumCaratWeight' => $product->getData('diamond_minumum_carat_total_weight_side2'),
                    'minimumColor' => $product->getAttributeText('diamond_average_color_side2'),
                    'minimumClarity' => $product->getAttributeText('diamond_average_clarity_side2'),
                    'settingType' => $product->getAttributeText('diamond_setting_type_side2'),
                    'diamondShape' => $product->getAttributeText('diamond_shape_side2'),
                    'diamondQuality' => $product->getAttributeText('diamond_quality_side2'),
                ],
            ];

            $ringData = [
                "styleNumber" => $product->getSku(),
                "settingName" => $product->getName(),
                "description" => $product->getDescription(),
                "metalType" => $product->getAttributeText('metaltype'),
                "collection" => $product->getAttributeText('ringcollection'),
                "centerStoneFit" => $product->getAttributeText('gemstoneshape'),
                "centerStoneMinCarat" => $centerStoneMinCarat,
                "centerStoneMaxCarat" => $centerStoneMaxCarat,
                "category" => "Engagement Ring - Complete",
                "settingId" => $id,
                "vendorId" => "4098",
                "vendorCompany" => "DIAMOND ICQ LIMITED",
                "vendorName" => "Pritesh",
                "vendorEmail" => "pritesh@diamondicq.com",
                "vendorPhone" => "+91 9537491847",
                "imageUrl" => $imageUrl,
                "cost" => $this->convertPrice($product->getFinalPrice()),
                "originalCost" => "",
                "mainImageURL" => $imageUrl,
                "roundImageURL" => "",
                "asscherImageURL" => "",
                "emeraldImageURL" => "",
                "radiantImageURL" => "",
                "cushionImageURL" => "",
                "marquiseImageURL" => "",
                "ovalImageURL" => "",
                "heartImageURL" => "",
                "pearImageURL" => "",
                "princessImageURL" => "",
                "dealerId" => null,
                "thumbNailImage" => null,
                "extraImage" => $extraImage,
                "configurableProduct" => [],
                "prongMetal" => "",
                "settingType" => "",
                "width" => "",
                "videoURL" => $this->getRingVideoUrl($product),
                "designerLogo" => null,
                "designerName" => "DIAMOND ICQ LIMITED",
                "isFavorite" => false,
                "ringSize" => [
                    "4",
                    "4.5",
                    "5",
                    "5.5",
                    "6",
                    "6.5",
                    "7",
                    "7.5",
                    "7",
                    "8",
                    "8.5",
                    "9",
                    "9.5",
                    "10",
                    "10.5",
                    "11",
                    "11.5",
                    "12",
                    "12.5",
                    "13",
                    "13.5",
                    "14",
                    "14.5",
                    "15",
                    "15.5",
                    "16",
                    "16.5",
                    "17",
                    "17.5",
                    "18",
                    "18.5",
                    "19",
                    "19.5",
                    "20",
                    "20.5",
                    "21",
                    "21.5",
                    "22",
                    "22.5",
                    "23",
                    "23.5"
                ],
                "sideStoneQuality" => [
                    $product->getAttributeText('gemstonequality')
                ],
                "currencyFrom" => "USD",
                "currencySymbol" => "US$",
                "metalID" => "",
                "colorID" => "",
                "internalUselink" => "No",
                "ringSizeType" => "American",
                "showPrice" => true,
                "rbEcommerce" => true,
                "showBuySettingOnly" => false,
                "tryon" => false,
                "showLightBrillianceURL" => false,
                "lightBrillianceURL" => "",
                "isLabSetting" => false,
                "retailerInfo" => (object) [
                    "retailerStockNo" => null,
                    "timingList" => $timingList,
                    "addressList" => $addressList,
                    'retailerName' => '',
                    'retailerEmail' => '',
                    'retailerContactNo' => '',
                ],
                "timingList" => $timingList,
                "addressList" => $addressList,
                "sideDiamondDetail1" => $diamondSideDetail
            ];
            $ringData['configurableProduct'] = $this->getRingConfigurableProducts($product);
            $returnData = ['ringData' => $ringData];
        }

        return $returnData;
    }

    public function sendRingRequest($requestParam)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFinalPrice();

        // filter attribute set "Ringbuilder"
        $attributeSetId = $this->getAttributeSetId('Ringbuilder');
        $collection->addAttributeToFilter('attribute_set_id', $attributeSetId);

        // filter status enabled
        $collection->addAttributeToFilter(
            'status',
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        );

        // add join to group configurable products
        $collection->getSelect()->join(
            ['link_table' => 'catalog_product_super_link'],
            'link_table.product_id = e.entity_id',
            ['product_id','parent_id']
        );
        $collection->getSelect()->group('link_table.parent_id');
        $group_products = true;

        // apply ring collection filter - ring_collection
        if (!empty($requestParam['ring_collection'])) {
            $options = $this->getAttributeOptionByCode('ringcollection');
            foreach ($options as $option) {
                if ($option->getData('label') == $requestParam['ring_collection']) {
                    $collection->addAttributeToFilter('ringcollection', $option->getData('value'));
                    break;
                }
            }
        }

        // apply shape filter - shapes
        if (!empty($requestParam['shapes'])) {
            $options = $this->getAttributeOptionByCode('gemstoneshape');
            foreach ($options as $option) {
                if ($option->getData('label') == $requestParam['shapes']) {
                    $collection->addAttributeToFilter('gemstoneshape', $option->getData('value'));
                    break;
                }
            }
        }

        // apply metal type filter - ring_metal
        if (!empty($requestParam['ring_metal'])) {
            $options = $this->getAttributeOptionByCode('metaltype');
            foreach ($options as $option) {
                if ($option->getData('label') == $requestParam['ring_metal']) {
                    $collection->addAttributeToFilter('metaltype', $option->getData('value'));
                    break;
                }
            }
        }

        // apply price filter - price_from, price_to
        $price_from = (int)$requestParam['price_from'];
        $price_to = (int)$requestParam['price_to'];
        $collection->getSelect()
            ->where("price_index.final_price >= " . $price_from)
            ->where("price_index.final_price <= " . $price_to);

        // apply sku search filter
        if (!empty($requestParam['settingId'])) {
            $search_keyword = $requestParam['settingId'];
            $collection->addAttributeToFilter('sku', ['like' => "%{$search_keyword}%"]);
            $collection->getSelect()->group('link_table.product_id');
            $group_products = false;
        }

        // sort list by price
        if ($requestParam['sort_by'] == 'cost') {
            $collection->addAttributeToSort('price', $requestParam['sort_direction']);
        }
        $collection->addAttributeToSort('sku', 'ASC');

        $query = $collection->getSelect()->__toString();
        $sqlSelectCount = "SELECT COUNT(*) FROM ({$query}) q";
        $total_items = (int)$this->connection->fetchOne($sqlSelectCount);

        $page_limit = $this->getResultPerPageforRing();
        $page_offset = ($requestParam['page_number']-1) * $page_limit;
        $sql = "{$query} LIMIT {$page_limit} OFFSET {$page_offset} ";
        $items = $this->connection->fetchAll($sql);

        $result = [
            'rings' => [],
            'total' => $total_items,
        ];

        foreach ($items as $item) {
            $product = $this->getProductById($item['product_id']);
            $_product_name = $product->getName();
            $_sku = $product->getSku();
            $_price = $product->getFinalPrice();
            $_product_image_url =  $this->getProductImageUrl($product->getImage());

            if ($product->getTypeId() == 'configurable') {
                $configProducts = $product->getTypeInstance()->getUsedProducts($product);
                foreach ($configProducts as $_cp) {
                    if ($_price > 0 && $_cp->getPrice() > $_price) {
                        continue;
                    }
                    $_sku = $_cp->getSku();
                    $_price = $_cp->getFinalPrice();
                    $_product_image_url =  $this->getProductImageUrl($_cp->getImage());
                }
            } elseif ($group_products) {
                $parentProduct = $this->getProductById($item['parent_id']);
                $_product_name = $parentProduct->getName();
            }
            $_price = $this->convertPrice($_price);
            $_ring_data = [
                "settingId" => $_sku,
                "vendorId" => "4098",
                "name" => "{$_product_name}",
                "imageUrl" => $_product_image_url,
                "cost" => $_price,
                "centerStoneMinCarat" => "0.3",
                "centerStoneMaxCarat" => "3",
                "originalCost" => "",
                "mainImageURL" => $_product_image_url,
                "roundImageURL" => "",
                "asscherImageURL" => "",
                "emeraldImageURL" => "",
                "radiantImageURL" => "",
                "cushionImageURL" => "",
                "marquiseImageURL" => "",
                "ovalImageURL" => "",
                "heartImageURL" => "",
                "pearImageURL" => "",
                "princessImageURL" => "",
                "dealerId" => "4098",
                "stockNumber" => $_sku,
                "isFavorite" => false,
                "favId" => null,
                "designerLogo" => null,
                "currencyFrom" => "USD",
                "currencySymbol" => "US$",
                "priceSettingId" => $_sku,
                "videoURL" => $product->getVideourl(),
                "showPrice" => true,
                "isLabSetting" => false
            ];
            $result['rings'][] = (object)$_ring_data;
        }

        return $result;
    }
}
