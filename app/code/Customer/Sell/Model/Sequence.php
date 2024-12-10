<?php declare(strict_types=1);

namespace Customer\Sell\Model;

use Magento\Framework\DB\Sequence\SequenceInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection as AppResource;
use Customer\Sell\Model\ResourceModel\SequenceProfile as ResourceProfile;
use Magento\Framework\Exception\LocalizedException;

class Sequence implements SequenceInterface
{
    const DEFAULT_PATTERN  = "%s%'.06d";
    const DEFAULT_TRADE_TYPE = 'T';

    /**
     * @var string
     */
    private $lastIncrementId;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $type;

    /**
     * @var ResourceProfile
     */
    private $profileResource;

    /**
     * Sequence constructor.
     * @param AppResource $resource
     * @param ResourceProfile $profileResource
     * @param string $pattern
     * @param string $type
     */
    public function __construct(
        AppResource $resource,
        ResourceProfile $profileResource,
        string $pattern = self::DEFAULT_PATTERN,
        string $type = self::DEFAULT_TRADE_TYPE
    ) {
        $this->profileResource = $profileResource;
        $this->connection = $resource->getConnection();
        $this->pattern = $pattern;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSequenceTable()
    {
        return $this->connection->getTableName('sequence_sell_' . $this->getPrefix());
    }

    /**
     * Set type
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return strtolower($this->type);
    }

    /**
     * @return string|null
     * @throws LocalizedException
     */
    public function getCurrentValue()
    {
        if (!isset($this->lastIncrementId)) {
            return null;
        }
        return sprintf(
            $this->pattern,
            strtoupper($this->getPrefix()),
            $this->calculateCurrentValue()
        );
    }

    /**
     * @return string|null
     * @throws LocalizedException
     */
    public function getNextValue()
    {
        $this->connection->insert($this->getSequenceTable(), []);
        $this->lastIncrementId = $this->connection->lastInsertId($this->getSequenceTable());
        return $this->getCurrentValue();
    }

    /**
     * Calculate current value depends on start value
     *
     * @return string
     * @throws LocalizedException
     */
    private function calculateCurrentValue()
    {
        return ($this->lastIncrementId - $this->loadProfileByPrefix()->getStartValue())
            * $this->loadProfileByPrefix()->getStep() + $this->loadProfileByPrefix()->getStartValue();
    }

    /**
     * @return SequenceProfile
     * @throws LocalizedException
     */
    private function loadProfileByPrefix()
    {
        return $this->profileResource->loadByPrefix($this->type);
    }
}
