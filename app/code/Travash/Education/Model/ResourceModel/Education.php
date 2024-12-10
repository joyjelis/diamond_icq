<?php
namespace Travash\Education\Model\ResourceModel;

use Magento\Store\Model\Store;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;

class Education extends AbstractDb
{

    protected $store = null;
    protected $connection = null;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    protected function _construct()
    {
        $this->_init('travash_education', 'education_id');
    }

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    
    /**
     * @param $urlKey
     * @return mixed
     */
    public function checkUrlKey($urlKey)
    {
        $select = $this->getLoadByUrlKeySelect($urlKey, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns('travash_education.education_id')
            ->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param $urlKey
     * @param int $isActive
     * @return mixed
     */
    protected function getLoadByUrlKeySelect($urlKey, $isActive)
    {
        $storeId =  $this->_storeManager->getStore()->getId();
        $select = $this->getConnection()
            ->select()
            ->from('travash_education')
            ->where(
                'travash_education.url_key = ?',
                $urlKey
            )
            ->where(
                'travash_education.store_id = ?',
                $storeId
            );
        if ($isActive != '') {
            $select->where('travash_education.is_active = ?', $isActive);
        }
        return $select;
    }
}
