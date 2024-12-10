<?php
namespace Customer\Sell\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('customer_sell'),
                'certificate_remark',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M',
                    'nullable' => true,
                    'comment' => 'certificate remark'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            $targetTable = $installer->getTable('sequence_sell_q');
            $sourceTable = $installer->getTable('sequence_sell_p');

            $select = $installer->getConnection()->select()
                ->from('INFORMATION_SCHEMA.TABLES', 'AUTO_INCREMENT')
                ->where('TABLE_NAME = :table_name');

            $bind = [':table_name' => (string)$targetTable];
            $target_auto_increment = (int)$installer->getConnection()->fetchOne($select, $bind);

            $bind = [':table_name' => (string)$sourceTable];
            $source_auto_increment = (int)$installer->getConnection()->fetchOne($select, $bind);

            // make sure to check that target auto increment is not yet increased or used
            if ($target_auto_increment == 1 && $source_auto_increment > 1) {
                // apply source auto increment to target table
                $sql = "ALTER TABLE {$targetTable} AUTO_INCREMENT = {$source_auto_increment}";
                $installer->getConnection()->query($sql);
            }
        }

        $installer->endSetup();
    }
}
