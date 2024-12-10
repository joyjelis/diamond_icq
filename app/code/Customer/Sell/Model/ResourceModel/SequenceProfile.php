<?php declare(strict_types=1);

namespace Customer\Sell\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Customer\Sell\Model\SequenceProfileFactory;

class SequenceProfile extends AbstractDb
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sell_sequence_profile';

    /**
     * @var SequenceProfileFactory
     */
    protected $profileFactory;

    /**
     * SequenceProfile constructor.
     * @param Context $context
     * @param SequenceProfileFactory $profileFactory
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        SequenceProfileFactory $profileFactory,
        $connectionName = null
    ) {
        $this->profileFactory = $profileFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sell_sequence_profile', 'profile_id');
    }

    /**
     * @param $prefix
     * @return \Customer\Sell\Model\SequenceProfile
     * @throws LocalizedException
     */
    public function loadByPrefix($prefix)
    {
        $profile = $this->profileFactory->create();
        $connection = $this->getConnection();
        $bind = ['prefix' => strtoupper($prefix)];
        $select = $connection->select()
            ->from($this->getMainTable(), ['profile_id'])
            ->where('prefix = :prefix');
        $profileId = $connection->fetchOne($select, $bind);

        if ($profileId) {
            $this->load($profile, $profileId);
        }

        return $profile;
    }
}
