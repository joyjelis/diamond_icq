<?php

namespace Magneto\CustomProductDetails\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper {

	protected $eavConfig;
	const XML_PATH = "custom_utils/general/";
	const XML_ORDER_BY_TIME = "custom_utils/general/order_by_time";
	const XML_SHIPPING_VIA = "custom_utils/general/shipping_via";
	protected $priceCurrency;
	private $attributeSetRepository;

	const DEFAULT_PRECISION = "0";
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $_groupCollection,
		\Magento\Catalog\Api\AttributeSetRepositoryInterface $attributeSetRepository,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection,
		\Magento\Eav\Model\Config $eavConfig
	) {
		$this->eavConfig = $eavConfig;
		$this->timezoneInterface = $timezoneInterface;
		$this->productRepository = $productRepository;
		$this->priceCurrency = $priceCurrency;
		$this->_groupCollection = $_groupCollection;
		$this->attributeSetRepository = $attributeSetRepository;
		$this->_attributeSetCollection = $attributeSetCollection;
		parent::__construct($context);
	}

	public function getConfig($config) {
		return $this->scopeConfig->getValue(
			$config,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function getOrderByTime() {
		return $this->getConfig(self::XML_ORDER_BY_TIME);
	}
	public function getShippingMethod() {
		return $this->getConfig(self::XML_SHIPPING_VIA);
	}
	public function showDeliveryDateFormat($currentDate) {
		//$finalDateFormat = 'l, d F Y';
		$finalDateFormat = 'D, d M Y';
		//$expectedDeliveryDate = date($finalDateFormat, strtotime($currentDate));
		/*$expectedDeliveryDate = $this->timezoneInterface->formatDate(
	            $currentDate,
	            \IntlDateFormatter::FULL,
	            false
	        );
		*/
		$expectedDeliveryDate = $this->timezoneInterface->date(new \DateTime($currentDate))->format($finalDateFormat);
		return $expectedDeliveryDate;
	}
	public function getProductDeliveryDate($product) {
		$expectedDeliveryDate = '';
		if ($product) {
			if (null !== $product->getCustomAttribute('product_delivery_days')) {
				$productDeliveryDays = $product->getCustomAttribute('product_delivery_days')->getValue();
				if (trim($productDeliveryDays) != '' && trim($productDeliveryDays) != 0) {
					$currentDate = date('Y-m-d');
					$expectedDeliveryDate = date("Y-m-d", strtotime($currentDate . " + $productDeliveryDays day"));
					$expectedDeliveryDate = $this->showDeliveryDateFormat($expectedDeliveryDate);
				}
			}
		}
		return $expectedDeliveryDate;
	}
	public function getProductPrice($product) {
		$currentProductPrice = array();
		$regularPrice = $specialPrice = $discountPercentage = null;
		if ($product->getTypeId() == 'simple') {
			$regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();
			$specialPrice = $product->getPriceInfo()->getPrice('special_price')->getValue();
		} else if ($product->getTypeId() == 'configurable') {
			$basePrice = $product->getPriceInfo()->getPrice('regular_price');
			$regularPrice = $basePrice->getMinRegularAmount()->getValue();
			$specialPrice = $product->getFinalPrice();
		} else if ($product->getTypeId() == 'bundle') {
			$regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getMinimalPrice()->getValue();
			$specialPrice = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
		} else if ($product->getTypeId() == 'grouped') {
			$usedProds = $product->getTypeInstance(true)->getAssociatedProducts($product);
			foreach ($usedProds as $child) {
				if ($child->getId() != $product->getId()) {
					$regularPrice += $child->getPrice();
					$specialPrice += $child->getFinalPrice();
				}
			}
		}

		if (!empty($specialPrice)) {
			$specialprice = $product->getSpecialPrice();
			$specialPriceFromDate = $product->getSpecialFromDate();
			$specialPriceToDate = $product->getSpecialToDate();
			$today = time();
			if ($specialprice) {
				if ($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate) || $today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate)) {
					$discountPercentage = round((($regularPrice - $specialPrice) / $regularPrice) * 100);
				}
			}

		}

		$currentProductPrice['regular-price'] = !empty($regularPrice) ? $this->getCurrencyFormat(trim($regularPrice)) : null;
		$currentProductPrice['special-price'] = !empty($specialPrice) ? $this->getCurrencyFormat(trim($specialPrice)) : null;
		$currentProductPrice['discount-percentage'] = !empty($discountPercentage) ? trim($discountPercentage) : null;
		return $currentProductPrice;
	}
	public function getCurrencyFormat($price, $includeContainer = false, $precision = self::DEFAULT_PRECISION) {
		//Example: $this->priceCurrency->format($price,true,2);
		return $this->priceCurrency->format(
			$price,
			$includeContainer = true,
			$precision = self::DEFAULT_PRECISION,
			$scope = null,
			$currency = null
		);
	}

	public function getAttributeSet($attributeSetId) {
		try {
			$attributeSet = $this->attributeSetRepository->get($attributeSetId);
		} catch (Exception $exception) {
			throw new Exception($exception->getMessage());
		}

		return $attributeSet;
	}
	public function getAttributeSetId($attributeSetName) {
		$attributeSetCollection = $this->_attributeSetCollection->create()
			->addFieldToSelect('attribute_set_id')
			->addFieldToFilter('attribute_set_name', $attributeSetName)
			->getFirstItem()
			->toArray();

		$attributeSetId = (int) $attributeSetCollection['attribute_set_id'];
		// OR (see benchmark below for make your choice)
		$attributeSetId = (int) implode($attributeSetCollection);

		return $attributeSetId;
	}
	public function getAttributeSetDefaultGroupSettings($currentAttributeSetName) {
		$attributeSetArray = array();
		if (trim($currentAttributeSetName) != '') {
			$currentSettingName = self::XML_PATH . $currentAttributeSetName;
			$getValues = $this->getConfig($currentSettingName);
			if (trim($getValues) != '') {
				$getValuesExplodeArray = explode(",", $getValues);
				$attributeSetArray[$currentAttributeSetName] = $getValuesExplodeArray;
			}
		}
		return $attributeSetArray;
	}
	
	public function getProductAttributeGroupsDetail($product) {
		$attributeSetId = $product->getAttributeSetId();
		$currentAttributeSetDetails = $this->getAttributeSet($attributeSetId);
		$currentAttributeSetName = $currentAttributeSetDetails->getAttributeSetName();
		$attributeSetIdGroupsArray = $this->getAttributeSetDefaultGroupSettings(trim($currentAttributeSetName));

		$frontendGroups = array();
		if (array_key_exists($currentAttributeSetName, $attributeSetIdGroupsArray)) {
			
			$groupCollection = $this->_groupCollection->create()
				->setAttributeSetFilter($attributeSetId)
				->setSortOrder()
				->load();

			$i = 0;
			foreach ($groupCollection as $group) {
				$groupName = $group->getData('attribute_group_name');
				$groupCode = $group->getData('attribute_group_code');
				$groupId = $group->getData('attribute_group_id');
				if (in_array($groupCode, $attributeSetIdGroupsArray[$currentAttributeSetName])) {
					$attributes = $product->getAttributes($groupId, true);
					$frontendGroups[$i]['attribute_group_name'] = $groupName;
					$frontendGroups[$i]['attribute_group_code'] = $groupCode;
					$frontendGroups[$i]['attribute_group_id'] = $groupId;

					foreach ($attributes as $key => $attribute) {
						$attributeCode = $attribute->getAttributeCode();
						$attributeLabel = $attribute->getFrontend()->getLabel();
						$attributeStoreLabel = $attribute->getData('store_label');
						$attributeValue = $attribute->getFrontend()->getValue($product);
						if (is_string($attributeValue) && trim($attributeValue) != '') {
							$attributeOptionArray = array("label" => $attributeLabel, "value" => $attributeValue, "store_label" => $attributeStoreLabel, 'code' => $attributeCode);
							$frontendGroups[$i]['attribute_group_options'][] = $attributeOptionArray;
						}
					}
				}

				$i++;
			}
		}
		if (!empty($frontendGroups)) {
			$frontendGroups = array_values($frontendGroups);
		}

		$diamond_groups = [];

		foreach ($frontendGroups as $key => $value) {
			if (isset($value['attribute_group_options'])) {

				if ($value['attribute_group_code'] == "product-information") {
					$options = $value['attribute_group_options'];
					$jewellery_type = array_filter(
						$options,
						function ($element) {
							return strpos($element['code'], 'jewellery_type') !== false;
						}
					);

					foreach ($jewellery_type as $options) {
						if (isset($options['value']) && !empty($options['value'])) {
							$frontendGroups[$key]['attribute_group_name'] = str_replace("Product", __($options['value']), $value['attribute_group_name']);
							$frontendGroups[$key]['attribute_group_name'] = str_replace("Information", __("Information"), $frontendGroups[$key]['attribute_group_name']);
						}
					}
				}

				if ($value['attribute_group_code'] == "diamond-information") {
					$original = $value;
					unset($frontendGroups[$key]);
					$options = $value['attribute_group_options'];
					if (!empty($options)) {
						$centre_shape = array_filter(
							$options,
							function ($element) {
								return strpos($element['code'], 'centre') !== false;
							}
						);

						$diamond_groups[] = $this->Transformdata($original, $centre_shape);

						$side1_shape = array_filter(
							$options,
							function ($element) {
								return strpos($element['code'], 'side1') !== false;
							}
						);

						$diamond_groups[] = $this->Transformdata($original, $side1_shape);

						$side2_shape = array_filter(
							$options,
							function ($element) {
								return strpos($element['code'], 'side2') !== false;
							}
						);

						$diamond_groups[] = $this->Transformdata($original, $side2_shape);
					}
				}
			}
		}

		foreach ($diamond_groups as $key => $diamondval) {
			if (!empty($diamondval['attribute_group_options'])) {
				$frontendGroups[] = $diamond_groups[$key];
			}
		}
		
		return $frontendGroups;
	}

	public function Transformdata($original, $data) {
		$shape = array_filter(
			$data,
			function ($element) {
				return strpos($element['code'], 'diamond_shape') !== false;
			}
		);

		$group_data['attribute_group_name'] = __($original['attribute_group_name']);

		foreach ($shape as $options) {
			if (isset($options['value']) && !empty($options['value'])) {
				$group_data['attribute_group_name'] = __($options['value']) . ' ' . __($original['attribute_group_name']);
			}
		}

		$group_data['attribute_group_code'] = $original['attribute_group_code'];
		$group_data['attribute_group_id'] = $original['attribute_group_id'];
		$group_data['attribute_group_options'] = $data;

		return $group_data;
	}
}
