<?php
/**
 * Address
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Helper;

use Magento\Customer\Helper\Address as CustomerAddressHelper;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Directory\Model\Region;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Address extends Data
{
    const SORTED_FIELD_POSITION = 'magepow_onestepcheckout/field/position';

    /**
     * @type \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @type \Magento\Framework\Locale\Resolver
     */
    protected $_localeResolver;

    /**
     * @type \Magento\Directory\Model\Region
     */
    protected $_regionModel;

    /**
     * @var CustomerAddressHelper
     */
    protected $addressHelper;

    /**
     * @var AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * Address constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param DirectoryList $directoryList
     * @param Resolver $localeResolver
     * @param Region $regionModel
     * @param CustomerAddressHelper $addressHelper
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        Resolver $localeResolver,
        Region $regionModel,
        CustomerAddressHelper $addressHelper,
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    )
    {
        $this->_directoryList                = $directoryList;
        $this->_localeResolver               = $localeResolver;
        $this->_regionModel                  = $regionModel;
        $this->addressHelper                 = $addressHelper;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return array
     */
    public function getAddressFields()
    {
        $fieldPosition = $this->getAddressFieldPosition();

        $fields = array_keys($fieldPosition);
        if (!in_array('country_id', $fields)) {
            array_unshift($fields, 'country_id');
        }
        if (in_array('region_id', $fields)) {
            $fields[] = 'region_id_input';
        }
        return $fields;
    }

    /**
     * @return array
     */
    public function getAddressFieldPosition()
    {
        $fieldPosition = [];
        $sortedField   = $this->getSortedField();
        foreach ($sortedField as $field) {
            $fieldPosition[$field->getAttributeCode()] = [
                'sortOrder' => $field->getSortOrder(),
                'colspan'   => $field->getColspan(),
                'isNewRow'  => $field->getIsNewRow()
            ];
        }

        return $fieldPosition;
    }

    /**
     * @param bool|true $onlySorted
     * @return array
     */
    public function getSortedField($onlySorted = true)
    {
        $availableFields = [];
        $sortedFields    = [];
        $sortOrder       = 1;

        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $collection */
        $collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );
        foreach ($collection as $key => $field) {
            if (!$this->isAddressAttributeVisible($field)) {
                continue;
            }
            $availableFields[] = $field;
        }

        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $collection */
        $collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer',
            'customer_account_create'
        );
        foreach ($collection as $key => $field) {
            if (!$this->isCustomerAttributeVisible($field)) {
                continue;
            }
            $availableFields[] = $field;
        }

        $isNewRow    = true;
        $fieldConfig = $this->getFieldPosition();
        foreach ($fieldConfig as $field) {
            foreach ($availableFields as $key => $avField) {
                if ($field['code'] == $avField->getAttributeCode()) {
                    $avField->setColspan($field['colspan'])
                        ->setSortOrder($sortOrder++)
                        ->setIsNewRow($isNewRow);
                    $sortedFields[] = $avField;
                    unset($availableFields[$key]);

                    $this->checkNewRow($field['colspan'], $isNewRow);
                    break;
                }
            }
        }
        return $onlySorted ? $sortedFields : [$sortedFields, $availableFields];
    }

    /**
     * @param $attribute
     * @return bool|null|string
     */
    public function isAddressAttributeVisible($attribute)
    {
        $code   = $attribute->getAttributeCode();
        $result = $attribute->getIsVisible();
        switch ($code) {
            case 'vat_id':
                $result = $this->addressHelper->isVatAttributeVisible();
                break;
            case 'region':
                $result = false;
                break;
        }
        return $result;
    }

    /**
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @return bool|null|string
     */
    public function isCustomerAttributeVisible($attribute)
    {
        $code = $attribute->getAttributeCode();
        if (in_array($code, ['gender', 'taxvat'])) {
            return $attribute->getIsVisible();
        } else if (!$attribute->getIsUserDefined()) {
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getFieldPosition()
    {
        $fields = $this->getConfigValue(self::SORTED_FIELD_POSITION);

        return self::jsonDecode($fields);
    }

    /**
     * @param $colSpan
     * @param $isNewRow
     * @return $this
     */
    private function checkNewRow($colSpan, &$isNewRow)
    {
        if ($colSpan == 6 && $isNewRow) {
            $isNewRow = false;
        } else if ($colSpan == 12 || ($colSpan == 6 && !$isNewRow)) {
            $isNewRow = true;
        }
        return $this;
    }

    /***************************************** Maxmind Db GeoIp ******************************************************/
    /**
     * Check has library at path var/Magepow/OnestepCheckout/GeoIp/
     * @return bool|string
     */
    public function checkHasLibrary()
    {
        $path = $this->_directoryList->getPath('var') . '/Magepow/OnestepCheckout/GeoIp';
        if (!file_exists($path)) {
            return false;
        }

        $folder   = scandir($path, true);
        $pathFile = $path . '/' . $folder[0] . '/GeoLite2-City.mmdb';
        if (!file_exists($pathFile)) {
            return false;
        }

        return $pathFile;
    }


    /**
     * @return array
     */
    protected function getLocales()
    {
        $locale   = $this->_localeResolver->getLocale();
        $language = substr($locale, 0, 2) ? substr($locale, 0, 2) : 'en';

        $locales = [$language];
        if ($language != 'en') {
            $locales[] = 'en';
        }
        return $locales;
    }
}
