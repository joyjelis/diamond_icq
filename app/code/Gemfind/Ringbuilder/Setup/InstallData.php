<?php

namespace Gemfind\Ringbuilder\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        categorySetupFactory $categorySetupFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $attributeSet = $this->attributeSetFactory->create();
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

        $attributeSet->setEntityTypeId($entityTypeId)->load('Gemfind Ringbuilder', 'attribute_set_name');

        $data = [
            'attribute_set_name' => 'Gemfind Ringbuilder',
            'entity_type_id' => $entityTypeId,
            'sort_order' => 20,
        ];
        if (!$attributeSet->getId()) {
            $attributeSet->setData($data);
            $attributeSet->validate();
            $attributeSet->save();
            $attributeSet->initFromSkeleton($attributeSetId);
            $attributeSet->save();
        }

        $attributeSetDiamonds = $this->attributeSetFactory->create();
        $data = ['attribute_set_name' => 'Gemfind Diamonds','entity_type_id' => $entityTypeId,'sort_order' => 20,];
        $attributeSetDiamonds->setEntityTypeId($entityTypeId)->load('Gemfind Diamonds', 'attribute_set_name');
        if (!$attributeSetDiamonds->getId()) {
            $attributeSetDiamonds->setData($data);
            $attributeSetDiamonds->validate();
            $attributeSetDiamonds->save();
            $attributeSetDiamonds->initFromSkeleton($attributeSetId);
            $attributeSetDiamonds->save();
        }

        // Get emipro_sampletable table
        $tableName = $setup->getTable('url_rewrite');
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            // Declare data
            $data = [
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/round',
                    'target_path' => 'ringbuilder/diamond/index/shape/round',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/radiant',
                    'target_path' => 'ringbuilder/diamond/index/shape/radiant',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/princess',
                    'target_path' => 'ringbuilder/diamond/index/shape/princess',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/pear',
                    'target_path' => 'ringbuilder/diamond/index/shape/pear',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/oval',
                    'target_path' => 'ringbuilder/diamond/index/shape/oval',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/marquise',
                    'target_path' => 'ringbuilder/diamond/index/shape/marquise',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/heart',
                    'target_path' => 'ringbuilder/diamond/index/shape/heart',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/emerald',
                    'target_path' => 'ringbuilder/diamond/index/shape/emerald',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/cushion',
                    'target_path' => 'ringbuilder/diamond/index/shape/cushion',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/shape/asscher',
                    'target_path' => 'ringbuilder/diamond/index/shape/asscher',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/type/navfancycolored',
                    'target_path' => 'ringbuilder/diamond/index/type/navfancycolored',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/diamond/type/navlabgrown',
                    'target_path' => 'ringbuilder/diamond/index/type/navlabgrown',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/type/labsettings',
                    'target_path' => 'ringbuilder/settings/index/type/labsettings',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/halo',
                    'target_path' => 'ringbuilder/settings/index/ringshape/halo',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/singlerow',
                    'target_path' => 'ringbuilder/settings/index/ringshape/singlerow',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/threestone',
                    'target_path' => 'ringbuilder/settings/index/ringshape/threestone',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/bypass',
                    'target_path' => 'ringbuilder/settings/index/ringshape/bypass',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/pave',
                    'target_path' => 'ringbuilder/settings/index/ringshape/pave',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/multirow',
                    'target_path' => 'ringbuilder/settings/index/ringshape/multirow',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/fancyshape',
                    'target_path' => 'ringbuilder/settings/index/ringshape/fancyshape',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/vintage',
                    'target_path' => 'ringbuilder/settings/index/ringshape/vintage',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/trellis',
                    'target_path' => 'ringbuilder/settings/index/ringshape/trellis',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ],
                [
                    'entity_type' => 'custom',
                    'entity_id' => 0,
                    'request_path' => 'ringbuilder/settings/ringshape/solitaire',
                    'target_path' => 'ringbuilder/settings/index/ringshape/solitaire',
                    'redirect_type' => 0,
                    'store_id' => $this->_storeManager->getStore()->getStoreId(),
                    'description' => NULL,
                    'is_autogenerated' => 0,
                    'metadata' =>  NULL
                ]
            ];

            // Insert data to table
            foreach ($data as $item) {
                $select = $setup->getConnection()->select()->from(
                    $tableName
                )->where(
                    'entity_id = ?',
                    $item['entity_id']
                )->where(
                    'request_path = ?',
                    $item['request_path']
                )->where(
                    'target_path = ?',
                    $item['target_path']
                )->where(
                    'store_id = ?',
                    $item['store_id']
                );

                $result = $setup->getConnection()->fetchRow($select);

                if(empty($result)){
                    $setup->getConnection()->insert($tableName, $item);
                }
            }
        }

        
        // TO CREATE PRODUCT ATTRIBUTE
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'gemfind_diamond_shape')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gemfind_diamond_shape',
                [
                    'group' => 'Diamond Properties',
                    'type' => 'varchar',
                    'label' => 'Shape',
                    'backend' => '',
                    'input' => 'select',
                    'source' => 'Gemfind\Ringbuilder\Model\Config\Source\Options\Shape',
                    'required' => false,
                    'sort_order' => 10,
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'used_in_product_listing' => false,
                    'visible_on_front' => false
                ]
            );
        }

        if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'gemfind_diamond_intintensity')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gemfind_diamond_intintensity',
                [
                    'group' => 'Diamond Properties',
                    'type' => 'varchar',
                    'label' => 'IntIntensity',
                    'backend' => '',
                    'input' => 'select',
                    'source' => 'Gemfind\Ringbuilder\Model\Config\Source\Options\IntIntensity',
                    'required' => false,
                    'sort_order' => 20,
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'used_in_product_listing' => false,
                    'visible_on_front' => false
                ]
            );
        }

        if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'gemfind_diamond_cut')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gemfind_diamond_cut',
                [
                    'group' => 'Diamond Properties',
                    'type' => 'varchar',
                    'label' => 'Cut',
                    'backend' => '',
                    'input' => 'select',
                    'source' => 'Gemfind\Ringbuilder\Model\Config\Source\Options\Cut',
                    'required' => false,
                    'sort_order' => 30,
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'used_in_product_listing' => false,
                    'visible_on_front' => false
                ]
            );
        }

        if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'gemfind_diamond_type')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gemfind_diamond_type',
                [
                    'group' => 'Diamond Properties',
                    'type' => 'varchar',
                    'label' => 'Diamond Type',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 40,
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'used_in_product_listing' => true,
                    'visible_on_front' => true
                ]
            );
        }

        if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'gemfind_ring_collection')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gemfind_ring_collection',
                [
                    'group' => 'Ring Properties',
                    'type' => 'text',
                    'label' => 'Collection',
                    'backend' => '',
                    'input' => 'text',
                    'source' => '',
                    'required' => false,
                    'sort_order' => 30,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'used_in_product_listing' => false,
                    'visible_on_front' => false
                ]
            );
        }


        if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'gemfind_ring_shape')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gemfind_ring_shape',
                [
                    'group' => 'Ring Properties',
                    'type' => 'text',
                    'label' => 'Shape',
                    'backend' => '',
                    'input' => 'text',
                    'source' => '',
                    'required' => false,
                    'sort_order' => 30,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'used_in_product_listing' => false,
                    'visible_on_front' => false
                ]
            );
        }


        if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'gemfind_ring_metaltype')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gemfind_ring_metaltype',
                [
                    'group' => 'Ring Properties',
                    'type' => 'text',
                    'label' => 'Metal Type',
                    'backend' => '',
                    'input' => 'text',
                    'source' => '',
                    'required' => false,
                    'sort_order' => 30,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'used_in_product_listing' => false,
                    'visible_on_front' => false
                ]
            );
        }


        if(!$eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'gemfind_ring_sidestone')) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'gemfind_ring_sidestone',
                [
                    'group' => 'Ring Properties',
                    'type' => 'text',
                    'label' => 'Side Stone',
                    'backend' => '',
                    'input' => 'text',
                    'source' => '',
                    'required' => false,
                    'sort_order' => 30,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'used_in_product_listing' => false,
                    'visible_on_front' => false
                ]
            );
        }

        $setup->endSetup();
    }
}
