<?php
namespace Travash\Education\Model\ResourceModel;

use Magento\Store\Model\Store;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Educationcat
 * @package Travash\Education\Model\ResourceModel
 */
class Educationcat extends AbstractDb
{

    /**
     * @var null
     */
    protected $store = null;
    /**
     * @var AdapterInterface
     */
    private $connection;
    /**
     * @var AppResource
     */
    protected $_resource;

     /**
      * Store manager
      *
      * @var \Magento\Store\Model\StoreManagerInterface
      */
    protected $_storeManager;

    /**
     * Educationcat constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        AppResource $resource,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->connection = $resource->getConnection('core_write');
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('travash_education_category', 'education_cat_id');
    }
    

    /**
     * @param $urlKey
     * @return mixed
     */
    public function checkUrlKey($urlKey)
    {
        $select = $this->getLoadByUrlKeySelect($urlKey, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns('travash_education_category.education_cat_id')
            ->limit(1);
        return $this->connection->fetchOne($select);
    }

    /**
     * @param $urlKey
     * @param int $isActive
     * @return mixed
     */
    protected function getLoadByUrlKeySelect($urlKey, $isActive)
    {
        $storeId =  $this->_storeManager->getStore()->getId();
        $select = $this->connection
            ->select()
            ->from('travash_education_category')
            ->where(
                'travash_education_category.url_key = ?',
                $urlKey
            )
            ->where(
                'travash_education_category.store_id = ?',
                $storeId
            );
        if ($isActive != '') {
            $select->where('travash_education_category.is_active = ?', $isActive);
        }
        return $select;
    }
}
