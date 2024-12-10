<?php
namespace Travash\Education\Block\Education;

/**
 * Class EducationCategoryTree
 * @package Travash\Education\Block\Education
 */
class EducationCategoryTree extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Travash\Education\Helper\Data
     */
    protected $_dataHelper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Travash\Education\Model\EducationcatFactory
     */
    protected $_educationCategoryFactory;
    /**
     * @var \Travash\Education\Model\Categories
     */
    protected $_categories;
    /**
     * EducationCategoryTree constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Travash\Education\Model\EducationcatFactory $educationCategoryFactory
     * @param \Travash\Education\Model\Categories $categories
     * @param \Travash\Education\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Travash\Education\Model\EducationcatFactory $educationCategoryFactory,
        \Travash\Education\Model\Categories $categories,
        \Travash\Education\Helper\Data $dataHelper
    ) {
        $this->_educationCategoryFactory = $educationCategoryFactory;
        $this->_dataHelper = $dataHelper;
        $this->_categories = $categories;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getCategoryTree()
    {
        return $this->_categories->getfrontOptionArray();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getEducationcategories($id)
    {
        /* @phpstan-ignore-next-line */
        $collection = $this->_educationCategoryFactory->create()->load($id);
        return $collection;
    }

    public function getCategoryById($id)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        /* @phpstan-ignore-next-line */
        $collection = $this->_educationCategoryFactory->create()->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('education_cat_id', $id)
            ->addFieldToFilter(
                'store_id',
                [
                    ['finset' => $storeId],
                    ['eq' => 0]
                ]
            )->setOrder('sort_order', 'ASC');
        return $collection->getFirstItem();
    }

    /**
     * @return mixed
     */
    public function getEducationOnlyParentCategory()
    {
        $storeId =  $this->_storeManager->getStore()->getId();
        /* @phpstan-ignore-next-line */
        $collection = $this->_educationCategoryFactory->create()->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('parent_cat_id', null)
            ->addFieldToFilter(
                'store_id',
                [['finset' => $storeId], ['eq' => 0]]
            )->setOrder('sort_order', 'ASC');
        return $collection;
    }

    
    /**
     * @return mixed
     */
    public function getEducationcategory()
    {
        $storeId =  $this->_storeManager->getStore()->getId();
        /* @phpstan-ignore-next-line */
        $collection = $this->_educationCategoryFactory->create()
            ->getCollection()->addFieldToSelect('*')->addFieldToFilter('is_active', '1')
            ->addFieldToFilter(
                'store_id',
                [['finset' => $storeId], ['eq' => 0]]
            )->setOrder('sort_order', 'ASC');
        return $collection;
    }

    /**
     * @return mixed
     */
    public function getPageUrl()
    {
        $mains = $this->_dataHelper->getPageUrl();
        return $mains;
    }

    /**
     * @param $categoryUrl
     * @return mixed
     */
    public function getCategoryUrl($categoryUrl)
    {
        $categoryPrefix = $this->_dataHelper->getCategoryUrlPrifix();
        $categorySuffix = $this->_dataHelper->getCategoryUrlSuffix();
        return $this->getUrl(
            $categoryPrefix .
            '/'
            . $categoryUrl
            . '' .
            $categorySuffix
        );
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        /* @phpstan-ignore-next-line */
        return $this->_storeManager->getStore()->getBaseUrl();
    }
    
    public function getCurrentStoreName()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
