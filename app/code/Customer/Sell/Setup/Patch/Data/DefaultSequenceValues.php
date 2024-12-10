<?php declare (strict_types = 1);

namespace Customer\Sell\Setup\Patch\Data;

use Customer\Sell\Model\EntityPool;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class DefaultSequenceValues implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EntityPool
     */
    private $entityPool;

    /**
     * DefaultSequenceValues constructor.
     * @param EntityPool $entityPool
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        EntityPool $entityPool,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->entityPool = $entityPool;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheridoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheridoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheridoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $sellSequenceProfileTable = $connection->getTableName('sell_sequence_profile');
        $defaultValues = [];
        $entities = $this->entityPool->getEntities();
        foreach ($entities as $entityType) {
            $prefix = substr($entityType, 0, 1);
            $defaultValues[] = [
                'prefix' => strtoupper($prefix),
                'start_value' => 1,
                'step' => 1,
            ];
        }

        $connection->insertMultiple(
            $sellSequenceProfileTable,
            $defaultValues
        );

        $this->moduleDataSetup->endSetup();
    }
}
