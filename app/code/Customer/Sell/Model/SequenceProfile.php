<?php declare(strict_types=1);

namespace Customer\Sell\Model;

use Magento\Framework\Model\AbstractModel;

class SequenceProfile extends AbstractModel
{
    /**
     * Default pattern for Sequence
     */
    const DEFAULT_PATTERN  = "%s%'.06d";

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\SequenceProfile::class);
    }
}
