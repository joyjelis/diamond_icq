<?php
namespace MageArray\Faq\Model\ResourceModel;

use Magento\Store\Model\Store;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Faqcat
 * @package MageArray\Faq\Model\ResourceModel
 */
class Faqcat extends AbstractDb
{

    /**
     * @var null
     */
    protected $store = null;
    /**
     * @var null
     */
    protected $connection = null;
    protected $_resource;
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('magearray_faq_category', 'faq_cat_id');
    }

    /**
     * Faqcat constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @param $urlKey
     * @return mixed
     */
    public function checkUrlKey($urlKey)
    {
        $select = $this->getLoadByUrlKeySelect($urlKey, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns('magearray_faq_category.faq_cat_id')
            ->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param $urlKey
     * @param null $isActive
     * @return mixed
     */
    protected function getLoadByUrlKeySelect($urlKey, $isActive = null)
    {
        $select = $this->getConnection()
            ->select()
            ->from('magearray_faq_category')
            ->where(
                'magearray_faq_category.url_key = ?',
                $urlKey
            );
        if (!empty($isActive)) {
            $select->where('magearray_faq_category.is_active = ?', $isActive);
        }
        return $select;
    }

    /**
     * @return null
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resources->getConnection('core_write');
        }
        return $this->connection;
    }
}
