<?php

namespace Customer\Sell\Model\Data;

use Customer\Sell\Api\Data\SellInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Customer\Sell\Model\ResourceModel\Sell as ResourceModelSell;

class Sell extends AbstractExtensibleModel implements SellInterface
{

    protected function _construct()
    {
        $this->_init(ResourceModelSell::class);
    }

    public function getSellId()
    {
        return $this->_getData(self::SELL_ID);
    }

    public function setSellId($value)
    {
        return $this->setData(self::SELL_ID, $value);
    }

    public function getQuote()
    {
        return $this->_getData(self::QUOTE);
    }

    public function setQuote($value)
    {
        return $this->setData(self::QUOTE, $value);
    }

    public function getTrade()
    {
        return $this->_getData(self::TRADE);
    }

    public function setTrade($value)
    {
        return $this->setData(self::TRADE, $value);
    }

    public function getCertificate()
    {
        return $this->_getData(self::CERTIFICATE);
    }

    public function setCertificate($value)
    {
        return $this->setData(self::CERTIFICATE, $value);
    }

    public function getEmail()
    {
        return $this->_getData(self::EMAIL);
    }

    public function setEmail($value)
    {
        return $this->setData(self::EMAIL, $value);
    }

    public function getMobile()
    {
        return $this->_getData(self::MOBILE);
    }

    public function setMobile($value)
    {
        return $this->setData(self::MOBILE, $value);
    }

    public function getImage()
    {
        return $this->_getData(self::IMAGE);
    }

    public function setImage($value)
    {
        return $this->setData(self::IMAGE, $value);
    }

    public function getItemImages()
    {
        return $this->_getData("item_images");
    }

    public function setItemImages($value)
    {
        return $this->setData("item_images", $value);
    }

    public function getCertificateImages()
    {
        return $this->_getData("certificate_images");
    }

    public function setCertificateImages($value)
    {
        return $this->setData("certificate_images", $value);
    }

    public function getName()
    {
        return $this->_getData(self::NAME);
    }

    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    public function getColor()
    {
        return $this->_getData(self::COLOR);
    }

    public function setColor($value)
    {
        return $this->setData(self::COLOR, $value);
    }

    public function getClarity()
    {
        return $this->_getData(self::CLARITY);
    }

    public function setClarity($value)
    {
        return $this->setData(self::CLARITY, $value);
    }

    public function getCarat()
    {
        return $this->_getData(self::CARAT);
    }

    public function setCarat($value)
    {
        return $this->setData(self::CARAT, $value);
    }

    public function getCut()
    {
        return $this->_getData(self::CUT);
    }

    public function setCut($value)
    {
        return $this->setData(self::CUT, $value);
    }

    public function getPolish()
    {
        return $this->_getData(self::POLISH);
    }

    public function setPolish($value)
    {
        return $this->setData(self::POLISH, $value);
    }

    public function getSymmetry()
    {
        return $this->_getData(self::SYMMETRY);
    }

    public function setSymmetry($value)
    {
        return $this->setData(self::SYMMETRY, $value);
    }

    public function getFluorescence()
    {
        return $this->_getData(self::FLUORESCENCE);
    }

    public function setFluorescence($value)
    {
        return $this->setData(self::FLUORESCENCE, $value);
    }

    public function getLab()
    {
        return $this->_getData(self::LAB);
    }

    public function setLab($value)
    {
        return $this->setData(self::LAB, $value);
    }

    public function getIsCertify()
    {
        return $this->_getData(self::IS_CERTIFY);
    }

    public function setIsCertify($value)
    {
        return $this->setData(self::IS_CERTIFY, $value);
    }

    public function getQualityOfPrice()
    {
        return $this->_getData(self::QUALITYOFPRICE);
    }

    public function setQualityOfPrice($value)
    {
        return $this->setData(self::QUALITYOFPRICE, $value);
    }

    public function getCertificateNo()
    {
        return $this->_getData(self::CERTIFICATE_NO);
    }

    public function setCertificateNo($value)
    {
        return $this->setData(self::CERTIFICATE_NO, $value);
    }

    public function getPrice()
    {
        return $this->_getData(self::PRICE);
    }

    public function setPrice($value)
    {
        return $this->setData(self::PRICE, $value);
    }

    public function getAcceptOffer()
    {
        return $this->_getData(self::ACCEPT_OFFER);
    }

    public function setAcceptOffer($value)
    {
        return $this->setData(self::ACCEPT_OFFER, $value);
    }

    public function getBankName()
    {
        return $this->_getData(self::BANK_NAME);
    }

    public function setBankName($value)
    {
        return $this->setData(self::BANK_NAME, $value);
    }

    public function getAccountName()
    {
        return $this->_getData(self::ACCOUNT_NAME);
    }

    public function setAccountName($value)
    {
        return $this->setData(self::ACCOUNT_NAME, $value);
    }

    public function getAccountNo()
    {
        return $this->_getData(self::ACCOUNT_NO);
    }

    public function setAccountNo($value)
    {
        return $this->setData(self::ACCOUNT_NO, $value);
    }

    public function getCountry()
    {
        return $this->_getData(self::COUNTRY);
    }

    public function setCountry($value)
    {
        return $this->setData(self::COUNTRY, $value);
    }

    public function getSwiftCode()
    {
        return $this->_getData(self::SWIFT_CODE);
    }

    public function setSwiftCode($value)
    {
        return $this->setData(self::SWIFT_CODE, $value);
    }

    public function getTradeType()
    {
        return $this->_getData(self::TRADE_TYPE);
    }

    public function setTradeType($value)
    {
        return $this->setData(self::TRADE_TYPE, $value);
    }

    public function getItemArrived()
    {
        return $this->_getData(self::ITEM_ARRIVED);
    }

    public function setItemArrived($value)
    {
        return $this->setData(self::ITEM_ARRIVED, $value);
    }

    public function getShipBy()
    {
        return $this->_getData(self::SHIP_BY);
    }

    public function setShipBy($value)
    {
        return $this->setData(self::SHIP_BY, $value);
    }

    public function getItemInspected()
    {
        return $this->_getData(self::ITEM_INSPECTED);
    }

    public function setItemInspected($value)
    {
        return $this->setData(self::ITEM_INSPECTED, $value);
    }

    public function getProcessBy()
    {
        return $this->_getData(self::PROCESS_BY);
    }

    public function setProcessBy($value)
    {
        return $this->setData(self::PROCESS_BY, $value);
    }

    public function getInspectionPass()
    {
        return $this->_getData(self::INSPECTION_PASS);
    }

    public function setInspectionPass($value)
    {
        return $this->setData(self::INSPECTION_PASS, $value);
    }

    public function getPayShipping()
    {
        return $this->_getData(self::PAY_SHIPPING);
    }

    public function setPayShipping($value)
    {
        return $this->setData(self::PAY_SHIPPING, $value);
    }

    public function getTransaction()
    {
        return $this->_getData(self::TRANSACTION);
    }

    public function setTransaction($value)
    {
        return $this->setData(self::TRANSACTION, $value);
    }

    public function getAcceptTransaction()
    {
        return $this->_getData(self::ACCEPT_TRANSACTION);
    }

    public function setAcceptTransaction($value)
    {
        return $this->setData(self::ACCEPT_TRANSACTION, $value);
    }

    public function getScheduleDate()
    {
        return $this->_getData(self::SCHEDULE_DATE);
    }

    public function setScheduleDate($value)
    {
        return $this->setData(self::SCHEDULE_DATE, $value);
    }

    public function getScheduleAccept()
    {
        return $this->_getData(self::SCHEDULE_ACCEPT);
    }

    public function setScheduleAccept($value)
    {
        return $this->setData(self::SCHEDULE_ACCEPT, $value);
    }

    public function getReconsiderPrice()
    {
        return $this->_getData(self::RECONSIDER_PRICE);
    }

    public function setReconsiderPrice($value)
    {
        return $this->setData(self::RECONSIDER_PRICE, $value);
    }

    public function getOfferPrice()
    {
        return $this->_getData(self::OFFER_PRICE);
    }

    public function setOfferPrice($value)
    {
        return $this->setData(self::OFFER_PRICE, $value);
    }

    public function getOfferConsignment()
    {
        return $this->_getData(self::OFFER_CONSIGNMENT);
    }

    public function setOfferConsignment($value)
    {
        return $this->setData(self::OFFER_CONSIGNMENT, $value);
    }

    public function getNumberOfItems()
    {
        return $this->_getData(self::NUMBER_OF_ITEMS);
    }

    public function setNumberOfItems($value)
    {
        return $this->setData(self::NUMBER_OF_ITEMS, $value);
    }

    public function getJewelleryType()
    {
        return $this->_getData(self::JEWELLERY_TYPE);
    }

    public function setJewelleryType($value)
    {
        return $this->setData(self::JEWELLERY_TYPE, $value);
    }

    public function getDescription()
    {
        return $this->_getData(self::DESCRIPTION);
    }

    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    public function getCertificateRemark()
    {
        return $this->_getData(self::CERTIFICATE_REMARK);
    }

    public function setCertificateRemark($value)
    {
        return $this->setData(self::CERTIFICATE_REMARK, $value);
    }

    public function getCustomer()
    {
        return $this->_getData(self::CUSTOMER);
    }

    public function setCustomer($value)
    {
        return $this->setData(self::CUSTOMER, $value);
    }

    public function getCustomerId()
    {
        return $this->_getData(self::CUSTOMER_ID);
    }

    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    public function getInternal()
    {
        return $this->_getData(self::INTERNAL);
    }

    public function setInternal($value)
    {
        return $this->setData(self::INTERNAL, $value);
    }

    public function getTracking()
    {
        return $this->_getData(self::TRACKING);
    }

    public function setTracking($value)
    {
        return $this->setData(self::TRACKING, $value);
    }

    public function getTransactionCustomer()
    {
        return $this->_getData(self::TRANSACTION_CUSTOMER);
    }

    public function setTransactionCustomer($value)
    {
        return $this->setData(self::TRANSACTION_CUSTOMER, $value);
    }

    public function getReason()
    {
        return $this->_getData(self::REASON);
    }

    public function setReason($value)
    {
        return $this->setData(self::REASON, $value);
    }

    public function getCustomerCountry()
    {
        return $this->_getData(self::CUSTOMER_COUNTRY);
    }

    public function setCustomerCountry($value)
    {
        return $this->setData(self::CUSTOMER_COUNTRY, $value);
    }

    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($value)
    {
        return $this->setData(self::UPDATED_AT, $value);
    }
}
