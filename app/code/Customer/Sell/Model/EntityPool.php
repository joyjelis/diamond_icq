<?php declare(strict_types=1);

namespace Customer\Sell\Model;

/**
 * Class EntityPool
 *
 * Pool of entities that require sequence
 */
class EntityPool
{
    /**
     * @var array
     */
    protected $entities;

    /**
     * @param array $entities
     */
    public function __construct(array $entities = [])
    {
        $this->entities = $entities;
    }

    /**
     * Retrieve entities that require sequence
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
