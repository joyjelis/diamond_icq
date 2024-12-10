<?php
namespace Magneto\FreeEngraving\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav_attribute
         */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'free_engraving');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'engraving_font');
        
        $statusOptions = 'Magneto\FreeEngraving\Model\Config\Source\StatusOptions';

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'free_engraving',
            [
                'group' => 'Engraving',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Engraving',
                'input' => 'boolean',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'is_used_in_grid' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,grouped,configurable,downloadable,virtual,bundle'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'engraving_font',
            [
                'group' => 'Engraving',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Engraving Fonts',
                'input' => 'select',
                'class' => '',
                'source' => $statusOptions,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'is_used_in_grid' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,grouped,configurable,downloadable,virtual,bundle'
            ]
        );
    }
}
