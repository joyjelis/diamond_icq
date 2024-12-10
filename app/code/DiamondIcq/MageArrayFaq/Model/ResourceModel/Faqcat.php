<?php
namespace DiamondIcq\MageArrayFaq\Model\ResourceModel;

/**
 * Class Faqcat
 * @package DiamondIcq\MageArrayFaq\Model\ResourceModel
 */
class Faqcat extends \MageArray\Faq\Model\ResourceModel\Faqcat
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $urlKey
     * @param null $isActive
     * @return mixed
     */
    protected function getLoadByUrlKeySelect($urlKey, $isActive = null)
    {
        $select = parent::getLoadByUrlKeySelect($urlKey, $isActive);
        
        $storeId = $this->storeManager->getStore()->getId();
        if (!empty($storeId)) {
            $select->where('magearray_faq_category.store_id = ?', $storeId);
        }
        return $select;
    }
}
