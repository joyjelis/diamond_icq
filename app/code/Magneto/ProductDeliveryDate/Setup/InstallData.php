<?php
namespace Magneto\ProductDeliveryDate\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Sales\Model\Order;

class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    protected $salesSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $installer = $setup;
        $installer->startSetup();

        /**
         * Add attributes to the eav_attribute
         */
        $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $installer]);

        
        /**
         * Add attributes to the eav_attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_delivery_days',
            [
                'group'        => 'Product Delivery Days',
                'type'         => 'varchar',
                'backend'      => '',
                'frontend'     => '',
                'label'        => 'Delivery Days',
                'input'        => 'text',
                'frontend_class' => 'required-entry',
                'global'       => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible'      => true,
                'required'     => false,
                'user_defined' => false,
                'default'      => '',
                'searchable'   => false,
                'filterable'   => false,
                'comparable'   => false,
                'unique'       => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => true
            ]
        );

        $salesSetup->addAttribute(Order::ENTITY, 'estimated_delivery_date', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> 255,
            'visible' => false,
            'nullable' => true
        ]);
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_grid'),
            'estimated_delivery_date',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' =>'Estimated Delivery Date'
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'estimated_delivery_date',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' =>'Estimated Delivery Date'
            ]
        );

        $installer->endSetup();
    }
}
