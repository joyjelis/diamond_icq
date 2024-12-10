<?php
namespace Travash\Education\Block\Education;

/**
 * Class EducationList
 * @package Travash\Education\Block\Education
 */
class EducationList extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Travash\Education\Model\EducationFactory
     */
    protected $_modelEducationFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_educationCollection;
    /**
     * @var \Travash\Education\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
    /**
     * @var \Travash\Education\Model\EducationcatFactory
     */
    protected $_educationCategoryFactory;
    /**
     * @var \Travash\Education\Model\Categories
     */
    protected $_categories;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * EducationList constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Travash\Education\Model\EducationFactory $modelEducationFactory
     * @param \Travash\Education\Model\EducationcatFactory $educationCategoryFactory
     * @param \Travash\Education\Helper\Data $dataHelper
     * @param \Travash\Education\Model\Categories $categories
     * @param \Magento\Cms\Model\Template\FilterProvider $filterContent
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Travash\Education\Model\EducationFactory $modelEducationFactory,
        \Travash\Education\Model\EducationcatFactory $educationCategoryFactory,
        \Travash\Education\Helper\Data $dataHelper,
        \Travash\Education\Model\Categories $categories,
        \Magento\Cms\Model\Template\FilterProvider $filterContent,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_modelEducationFactory = $modelEducationFactory;
        $this->_educationCategoryFactory = $educationCategoryFactory;
        $this->_dataHelper = $dataHelper;
        $this->_categories = $categories;
        $this->_filterProvider = $filterContent;
        $this->_storeManager = $storeManager;
    }

    /**
     * @return mixed
     */
    public function getCategoryTree()
    {
        return $this->_categories->getfrontOptionArray();
    }

    /**
     * Get filter description
     * @param  string $description
     * @return string
     */
    public function getFilterDescription($description)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        if (method_exists($this->_filterProvider->getBlockFilter(), 'setStoreId')) {
            return $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($description);
        }
        return '';
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
     * @param $id
     * @return mixed
     */
    public function getEducations($id)
    {
        /* @phpstan-ignore-next-line */
        $educationCollection = $this->_modelEducationFactory->create()->getCollection();
        if ($id) {
            $educationCollection->addFieldToFilter('education_category', ['finset' => $id]);
        }
        $storeId = $this->_storeManager->getStore()->getId();
        $educationCollection = $educationCollection->addFieldToFilter('is_active', ["eq" => 1]);
        $educationCollection = $educationCollection->addFieldToFilter(
            'store_id',
            [
                ['finset' => $storeId],
                ['eq' => 0]
            ]
        );
        return $educationCollection;
    }

    /**
     * @return mixed|null
     */
    public function getCollection()
    {
        if (!isset($this->_educationCollection)) {
            $this->_educationCollection = $this->getEducations(null);
            $this->_educationCollection
            ->setOrder(
                'publish_date'
            );
        }
        return $this->_educationCollection;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        /* @phpstan-ignore-next-line */
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData(
            'current_page'
        ) ? $this->getData(
            'current_page'
        ) : 1;
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

  

    public function getEducationcategory()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        /* @phpstan-ignore-next-line */
        $collection = $this->_educationCategoryFactory->create()
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter(
                'store_id',
                [
                    ['finset' => $storeId],
                    ['eq' => 0]
                ]
            )->setOrder('sort_order', 'ASC');
        return $collection;
    }

    public function getEducationChildCategory($id)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        /* @phpstan-ignore-next-line */
        $collection = $this->_educationCategoryFactory->create()
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('parent_cat_id', $id)
            ->addFieldToFilter(
                'store_id',
                [
                    ['finset' => $storeId],
                    ['eq' => 0]
                ]
            )->setOrder('sort_order', 'ASC');
        return $collection;
    }

    public function getCategoryInfo($cid)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        /* @phpstan-ignore-next-line */
        $collection = $this->_educationCategoryFactory->create()
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('education_cat_id', $cid)
            ->addFieldToFilter(
                'store_id',
                [
                    ['finset' => $storeId],
                    ['eq' => 0]
                ]
            )->setOrder('sort_order', 'ASC');
        return $collection->getFirstItem();
    }

    public function getCategoryById($id)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        /* @phpstan-ignore-next-line */
        $collection = $this->_educationCategoryFactory->create()
            ->getCollection()
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

    public function getEducationByCategoryId($id, $cid)
    {
        /* @phpstan-ignore-next-line */
        $educationCollection = $this->_modelEducationFactory->create()->getCollection();
        if ($id) {
            $educationCollection->addFieldToFilter('education_id', ['finset' => $id]);
        }
        if ($cid) {
            $educationCollection->addFieldToFilter('education_category', ['finset' => $cid]);
        }
        $storeId = $this->_storeManager->getStore()->getId();
        $educationCollection = $educationCollection->addFieldToFilter('is_active', ["eq" => 1]);
        $educationCollection = $educationCollection->addFieldToSelect('*')->addFieldToFilter(
            'store_id',
            [
                ['finset' => $storeId],
                ['eq' => 0]
            ]
        )->setOrder('sort_order', 'ASC');
        return $educationCollection->getFirstItem();
    }

    public function getEducationsByCategoryId($id, $cid)
    {
        /* @phpstan-ignore-next-line */
        $educationCollection = $this->_modelEducationFactory->create()->getCollection();
        if ($id) {
            $educationCollection->addFieldToFilter('education_id', ['finset' => $id]);
        }
        if ($cid) {
            $educationCollection->addFieldToFilter('education_category', ['finset' => $cid]);
        }
        $storeId = $this->_storeManager->getStore()->getId();
        $educationCollection = $educationCollection->addFieldToFilter('is_active', ["eq" => 1]);
        $educationCollection = $educationCollection->addFieldToSelect('*')->addFieldToFilter(
            'store_id',
            [
                ['finset' => $storeId],
                ['eq' => 0]
            ]
        )->setOrder('sort_order', 'ASC');
        return $educationCollection;
    }

    public function getNextEducationsByCategoryId($id, $cid)
    {
        /* @phpstan-ignore-next-line */
        $educationCollection = $this->_modelEducationFactory->create()->getCollection();
        if ($id) {
            $educationCollection->addFieldToFilter('education_id', ['neq' => $id]);
        }
        if ($cid) {
            $educationCollection->addFieldToFilter('education_category', ['finset' => $cid]);
        }
        $storeId = $this->_storeManager->getStore()->getId();
        $educationCollection = $educationCollection->addFieldToFilter('is_active', ["eq" => 1]);
        $educationCollection = $educationCollection->addFieldToSelect('*')->addFieldToFilter(
            'store_id',
            [
                ['finset' => $storeId],
                ['eq' => 0]
            ]
        )->setOrder('sort_order', 'ASC');
        return $educationCollection;
    }

    /**
     * @return mixed
     */
    protected function _prepareLayout()
    {
        $metaTitle = $this->_dataHelper->getMetaTitle();
        $metaKeywords = $this->_dataHelper->getMetaKeyword();
        $metaDescription = $this->_dataHelper->getMetaDescription();
        $pageTitle = $this->_dataHelper->getPageTitle();
        $this->pageConfig->getTitle()->set(__('Educations'));
        $this->pageConfig->setKeywords(__('Educations'));
        $this->pageConfig->setDescription(__('Educations'));
        
        if ($metaTitle) {
            $this->pageConfig->getTitle()->set($metaTitle);
        }

        if ($metaKeywords) {
            $this->pageConfig->setKeywords($metaKeywords);
        }

        if ($metaDescription) {
            $this->pageConfig->setDescription($metaDescription);
        }

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if (method_exists($pageMainTitle, 'setPageTitle') && $pageTitle !== '') {
            $pageMainTitle->setPageTitle($pageTitle);
        } elseif (method_exists($pageMainTitle, 'setPageTitle')) {
            $pageMainTitle->setPageTitle(__('Educations'));
        }

        return parent::_prepareLayout();
    }

    /**
     * Get First Image for Sell record
     *
     * @param string $image
     * @return string
     */
    public function getEducationImage($image)
    {
        /* @phpstan-ignore-next-line */
        $mediaUrl = $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl .  $image;
    }


    
    /**
     * @return string
     */
    public function getEducationUrl($image)
    {
        /* @phpstan-ignore-next-line */
        $mediaUrl = $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl .  $image;
    }
}
