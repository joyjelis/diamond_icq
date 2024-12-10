<?php
namespace Travash\Education\Block\Education;

/**
 * Class EducationTop
 * @package Travash\Education\Block\Education
 */
class EducationTop extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Travash\Education\Model\EducationFactory
     */
    protected $_modelEducationFactory;
    /**
     * @var null
     */
    protected $_educationCollection = null;
    /**
     * @var \Travash\Education\Helper\Data
     */
    protected $_dataHelper;
    /**
     * @var \Travash\Education\Model\EducationcatFactory
     */
    protected $_educationCategoryFactory;
    /**
     * @var \Travash\Education\Model\Categories
     */
    protected $_categories;

    /**
     * EducationTop constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Travash\Education\Model\EducationFactory $modelEducationFactory
     * @param \Travash\Education\Model\EducationcatFactory $educationCategoryFactory
     * @param \Travash\Education\Helper\Data $dataHelper
     * @param \Travash\Education\Model\Categories $categories
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Travash\Education\Model\EducationFactory $modelEducationFactory,
        \Travash\Education\Model\EducationcatFactory $educationCategoryFactory,
        \Travash\Education\Helper\Data $dataHelper,
        \Travash\Education\Model\Categories $categories
    ) {
        parent::__construct($context);
        $this->_modelEducationFactory = $modelEducationFactory;
        $this->_educationCategoryFactory = $educationCategoryFactory;
        $this->_dataHelper = $dataHelper;
        $this->_categories = $categories;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getEducationTops($id)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        /* @phpstan-ignore-next-line */
        $educationCollection = $this->_modelEducationFactory->create()->getCollection();
        $educationCollection = $educationCollection->addFieldToFilter('is_active', ["eq" => 1]);
        $educationCollection = $educationCollection->addFieldToFilter('is_featured', ["eq" => 1]);
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
     * @return mixed
     */
    public function getDisplayMode()
    {
        $displayMode = $this->_dataHelper->getDisplayMode();
        return $displayMode;
    }
}
