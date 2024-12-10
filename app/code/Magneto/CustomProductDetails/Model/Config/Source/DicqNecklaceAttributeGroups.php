<?php

namespace Magneto\CustomProductDetails\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DicqNecklaceAttributeGroups implements OptionSourceInterface {

	const ATTRIBUTESET_NAME = "dicq_necklace";
	public function __construct(
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $_groupCollectionFactory,
		\Magento\Catalog\Api\AttributeSetRepositoryInterface $attributeSetRepository,
		\Magneto\CustomProductDetails\Helper\Data $helperData,
		\Magento\Eav\Model\Config $eavConfig
	) {
		$this->_groupCollectionFactory = $_groupCollectionFactory;
		$this->helperData = $helperData;
		$this->attributeSetRepository = $attributeSetRepository;
		$this->eavConfig = $eavConfig;
	}
	public function toOptionArray() {

		$options = $attributesArray = array();
		$attributeSetId = $this->helperData->getAttributeSetId(self::ATTRIBUTESET_NAME);
		$groupCollection = $this->_groupCollectionFactory->create()
			->setAttributeSetFilter($attributeSetId)
			->setSortOrder()
			->load();
		foreach ($groupCollection as $group) {
			$groupName = $group->getData('attribute_group_name');
			$groupCode = $group->getData('attribute_group_code');
			$groupId = $group->getData('attribute_group_id');
			$attributesArray[] = array('value' => $groupCode, 'label' => $groupName);
		}
		return $attributesArray;

	}
}