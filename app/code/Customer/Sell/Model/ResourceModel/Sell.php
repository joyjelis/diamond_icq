<?php declare (strict_types = 1);

namespace Customer\Sell\Model\ResourceModel;

use Customer\Sell\Model\ResourceModel\SequenceProfile as ResourceSequenceProfile;
use Customer\Sell\Model\SequenceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

class Sell extends AbstractDb
{
    /**
     * @var SequenceProfile
     */
    protected $resourceSequenceProfile;

    /**
     * @var SequenceFactory
     */
    private $sequenceFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Sell constructor.
     * @param Context $context
     * @param SequenceProfile $sequenceProfile
     * @param SequenceFactory $sequenceFactory
     * @param StoreManagerInterface $storeManager
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        ResourceSequenceProfile $sequenceProfile,
        SequenceFactory $sequenceFactory,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->sequenceFactory = $sequenceFactory;
        $this->resourceSequenceProfile = $sequenceProfile;
        $this->storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheridoc
     */
    protected function _construct()
    {
        $this->_init('customer_sell', 'sell_id');
    }

    /**
     * @param AbstractModel $object
     * @return $this|Sell
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $sequence = $this->sequenceFactory->create();
        if ($object->getData('generate_quote_id') !== null && $object->getData('generate_quote_id') == 0) {
            if ($object->getQuoteNoUpdate()) {
                return $this;
            }

            $quote = uniqid('TP#');
            $object->setData('quote', $quote);
            return $this;

        } else {

            if ($object->getData('jewellery_type') == "Trade for Bigger Diamond") {
                $type = 'T';
            } else {
                $type = 'Q';
            }

            if ($object->getData('jewellery_type') == "Request Quote") {
                $type = 'C';
            }

            $sequence->setType($type);
            $quote = $sequence->getNextValue();
            $object->setData('quote', $quote);

            /*
                             * Adding store id
            */
            $storeId = $this->storeManager->getStore()->getId();
            $object->setData('store_id', $storeId);
        }

        return $this;
    }
}
