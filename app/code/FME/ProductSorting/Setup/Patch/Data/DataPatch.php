<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME Calalog
 * @author    FME extensions <support@fmeextensions.com
>
 * @package   FME_ProductSorting
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/
)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
declare(strict_types=1);

namespace FME\ProductSorting\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class DataPatch implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;
    public $productCollection;
    public $eavSetupFactory;
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CollectionFactory $productCollectionFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productCollection = $productCollectionFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {

        $eavSetup = $this->eavSetupFactory->create(array('setup' => $this->moduleDataSetup));

       
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'saving',
            array(
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Biggest Saving',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global'=> \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_for_sort_by' => true,
                'unique' => false,
                'apply_to' => ''
            )
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'imageProduct',
            array(
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Image Info',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global'=> \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_for_sort_by' => true,
                'unique' => false,
                'apply_to' => ''
            )
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'outOfStock',
            array(
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Stock status',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global'=> \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_for_sort_by' => true,
                'unique' => false,
                'apply_to' => ''
            )
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'status_order',
            array(
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Order status',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global'=> \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_for_sort_by' => true,
                'unique' => false,
                'apply_to' => ''
            )
        );

        $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'created_at', 'used_for_sort_by', 0);
        $productCollection = $this->productCollection->create();
        $productIds = $productCollection->getColumnValues('entity_id');
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return array();
    }
}
