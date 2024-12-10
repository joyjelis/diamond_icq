<?php

namespace Customer\Sell\Api\Data;

interface SellInterface
{
    const SELL_ID = 'sell_id';
    const QUOTE = 'quote';
    const TRADE = 'trade';
    const CERTIFICATE = 'certificate';
    const CERTIFICATE_IMAGE = 'certificate_image';
    const CERTIFICATE_NAME = 'certificate_name';
    const EMAIL = 'email';
    const MOBILE = 'mobile';
    const IMAGE = 'image';
    const NAME = 'name';
    const COLOR = 'color';
    const CLARITY = 'clarity';
    const CARAT = 'carat';
    const CUT = 'cut';
    const POLISH = 'polish';
    const SYMMETRY = 'symmetry';
    const FLUORESCENCE = 'fluorescence';
    const LAB = 'lab';
    const IS_CERTIFY = 'is_certify';
    const QUALITYOFPRICE = 'qualityofprice';
    const CERTIFICATE_NO = 'certificate_no';
    const CERTIFICATE_REMARK = 'certificate_remark';
    const PRICE = 'price';
    const ACCEPT_OFFER = 'accept_offer';
    const BANK_NAME = 'bank_name';
    const ACCOUNT_NAME = 'account_name';
    const ACCOUNT_NO = 'account_no';
    const COUNTRY = 'country';
    const SWIFT_CODE = 'swift_code';
    const TRADE_TYPE = 'trade_type';
    const ITEM_ARRIVED = 'item_arrived';
    const SHIP_BY = 'ship_by';
    const ITEM_INSPECTED = 'item_inspected';
    const PROCESS_BY = 'process_by';
    const INSPECTION_PASS = 'inspection_pass';
    const PAY_SHIPPING = 'pay_shipping';
    const TRANSACTION = 'transaction';
    const ACCEPT_TRANSACTION = 'accept_transaction';
    const SCHEDULE_DATE = 'schedule_date';
    const SCHEDULE_ACCEPT = 'schedule_accept';
    const RECONSIDER_PRICE = 'reconsider_price';
    const OFFER_PRICE = 'offer_price';
    const OFFER_CONSIGNMENT = 'offer_consignemnt';
    const NUMBER_OF_ITEMS = 'number_of_items';
    const JEWELLERY_TYPE = 'jewellery_type';
    const DESCRIPTION = 'description';
    const CUSTOMER = 'customer';
    const CUSTOMER_ID = 'customer_id';
    const INTERNAL = 'internal';
    const TRACKING = 'tracking';
    const TRANSACTION_CUSTOMER = 'transaction_customer';
    const REASON = 'reason';
    const CUSTOMER_COUNTRY = 'customer_country';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get sell_id
     * @return string|null
     */
    public function getSellId();

    /**
     * Set sell_id
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setSellId($value);

    /**
     * Get Quote
     * @return string|null
     */
    public function getQuote();

    /**
     * Set Quote
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setQuote($value);

    /**
     * Get trade
     * @return string|null
     */
    public function getTrade();

    /**
     * Set trade
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setTrade($value);

    /**
     * Get Certificate
     * @return string|null
     */
    public function getCertificate();

    /**
     * Set Certificate
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCertificate($value);

    /**
     * Get Email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set Email
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setEmail($value);

    /**
     * Get Mobile
     * @return string|null
     */
    public function getMobile();

    /**
     * Set Mobile
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setMobile($value);

    /**
     * Get Image
     * @return string|null
     */
    public function getImage();

    /**
     * Set Image
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setImage($value);

    /**
     * Get Images
     * @return string[]
     */
    public function getItemImages();

    /**
     * Set Images
     * @param string[] $imagesArray
     * @return $this
     */
    public function setItemImages($imagesArray);

    /**
     * Get Certificates
     * @return string[]
     */
    public function getCertificateImages();

    /**
     * Set Certificates
     * @param string[] $imagesArray
     * @return $this
     */
    public function setCertificateImages($imagesArray);

    /**
     * Get Name
     * @return string|null
     */
    public function getName();

    /**
     * Set Name
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setName($value);

    /**
     * Get Color
     * @return string|null
     */
    public function getColor();

    /**
     * Set Color
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setColor($value);

    /**
     * Get Clarity
     * @return string|null
     */
    public function getClarity();

    /**
     * Set Clarity
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setClarity($value);

    /**
     * Get Carat
     * @return string|null
     */
    public function getCarat();

    /**
     * Set Carat
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCarat($value);

    /**
     * Get Cut
     * @return string|null
     */
    public function getCut();

    /**
     * Set Cut
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCut($value);

    /**
     * Get Polish
     * @return string|null
     */
    public function getPolish();

    /**
     * Set Polish
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setPolish($value);

    /**
     * Get Symmetry
     * @return string|null
     */
    public function getSymmetry();

    /**
     * Set Symmetry
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setSymmetry($value);

    /**
     * Get Fluorescence
     * @return string|null
     */
    public function getFluorescence();

    /**
     * Set Fluorescence
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setFluorescence($value);

    /**
     * Get Lab
     * @return string|null
     */
    public function getLab();

    /**
     * Set Lab
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setLab($value);

    /**
     * Get IsCertify
     * @return string|null
     */
    public function getIsCertify();

    /**
     * Set IsCertify
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setIsCertify($value);

    /**
     * Get QualityOfPrice
     * @return string|null
     */
    public function getQualityOfPrice();

    /**
     * Set QualityOfPrice
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setQualityOfPrice($value);

    /**
     * Get CertificateNo
     * @return string|null
     */
    public function getCertificateNo();

    /**
     * Set CertificateNo
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCertificateNo($value);

    /**
     * Get Price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set Price
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setPrice($value);

    /**
     * Get AcceptOffer
     * @return string|null
     */
    public function getAcceptOffer();

    /**
     * Set AcceptOffer
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setAcceptOffer($value);

    /**
     * Get BankName
     * @return string|null
     */
    public function getBankName();

    /**
     * Set BankName
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setBankName($value);

    /**
     * Get AccountName
     * @return string|null
     */
    public function getAccountName();

    /**
     * Set AccountName
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setAccountName($value);

    /**
     * Get AccountNo
     * @return string|null
     */
    public function getAccountNo();

    /**
     * Set AccountNo
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setAccountNo($value);

    /**
     * Get Country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set Country
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCountry($value);

    /**
     * Get SwiftCode
     * @return string|null
     */
    public function getSwiftCode();

    /**
     * Set SwiftCode
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setSwiftCode($value);

    /**
     * Get TradeType
     * @return string|null
     */
    public function getTradeType();

    /**
     * Set TradeType
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setTradeType($value);

    /**
     * Get ItemArrived
     * @return string|null
     */
    public function getItemArrived();

    /**
     * Set ItemArrived
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setItemArrived($value);

    /**
     * Get ShipBy
     * @return string|null
     */
    public function getShipBy();

    /**
     * Set ShipBy
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setShipBy($value);

    /**
     * Get ItemInspected
     * @return string|null
     */
    public function getItemInspected();

    /**
     * Set ItemInspected
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setItemInspected($value);

    /**
     * Get ProcessBy
     * @return string|null
     */
    public function getProcessBy();

    /**
     * Set ProcessBy
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setProcessBy($value);

    /**
     * Get InspectionPass
     * @return string|null
     */
    public function getInspectionPass();

    /**
     * Set InspectionPass
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setInspectionPass($value);

    /**
     * Get PayShipping
     * @return string|null
     */
    public function getPayShipping();

    /**
     * Set PayShipping
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setPayShipping($value);

    /**
     * Get Transaction
     * @return string|null
     */
    public function getTransaction();

    /**
     * Set Transaction
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setTransaction($value);

    /**
     * Get AcceptTransaction
     * @return string|null
     */
    public function getAcceptTransaction();

    /**
     * Set AcceptTransaction
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setAcceptTransaction($value);

    /**
     * Get ScheduleDate
     * @return string|null
     */
    public function getScheduleDate();

    /**
     * Set ScheduleDate
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setScheduleDate($value);

    /**
     * Get ScheduleAccept
     * @return string|null
     */
    public function getScheduleAccept();

    /**
     * Set ScheduleAccept
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setScheduleAccept($value);

    /**
     * Get ReconsiderPrice
     * @return string|null
     */
    public function getReconsiderPrice();

    /**
     * Set ReconsiderPrice
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setReconsiderPrice($value);

    /**
     * Get OfferPrice
     * @return string|null
     */
    public function getOfferPrice();

    /**
     * Set OfferPrice
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setOfferPrice($value);

    /**
     * Get OfferConsignment
     * @return string|null
     */
    public function getOfferConsignment();

    /**
     * Set OfferConsignment
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setOfferConsignment($value);

    /**
     * Get NumberOfItems
     * @return string|null
     */
    public function getNumberOfItems();

    /**
     * Set NumberOfItems
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setNumberOfItems($value);

    /**
     * Get JewelleryType
     * @return string|null
     */
    public function getJewelleryType();

    /**
     * Set JewelleryType
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setJewelleryType($value);

    /**
     * Get Description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set Description
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setDescription($value);

    /**
     * Get Certificate Remark
     * @return string|null
     */
    public function getCertificateRemark();

    /**
     * Set Certificate Remark
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCertificateRemark($value);

    /**
     * Get Customer
     * @return string|null
     */
    public function getCustomer();

    /**
     * Set Customer
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCustomer($value);

    /**
     * Get Customer ID
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set Customer ID
     * @param int $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCustomerId($value);

    /**
     * Get Internal
     * @return string|null
     */
    public function getInternal();

    /**
     * Set Internal
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setInternal($value);

    /**
     * Get Tracking
     * @return string|null
     */
    public function getTracking();

    /**
     * Set Tracking
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setTracking($value);

    /**
     * Get TransactionCustomer
     * @return string|null
     */
    public function getTransactionCustomer();

    /**
     * Set TransactionCustomer
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setTransactionCustomer($value);

    /**
     * Get Reason
     * @return string|null
     */
    public function getReason();

    /**
     * Set Reason
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setReason($value);

    /**
     * Get CustomerCountry
     * @return string|null
     */
    public function getCustomerCountry();

    /**
     * Set CustomerCountry
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCustomerCountry($value);

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set Status
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setStatus($value);

    /**
     * Get CreatedAt
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set CreatedAt
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setCreatedAt($value);

    /**
     * Get UpdatedAt
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set UpdatedAt
     * @param string $value
     * @return \Customer\Sell\Api\Data\SellInterface
     */
    public function setUpdatedAt($value);
}
