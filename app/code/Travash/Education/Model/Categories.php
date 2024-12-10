<?php
namespace Travash\Education\Model;

/**
 * Class Categories
 * @package Travash\Education\Model
 */
class Categories extends \Magento\Framework\View\Element\Template implements \Magento\Framework\Option\ArrayInterface
{

    /**
     *
     */
    const STATUS_CAT1 = 1;
    /**
     *
     */
    const STATUS_CAT2 = 2;
    /**
     *
     */
    const STATUS_CAT3 = 3;

    /**
     * @var \Travash\Education\Model\EducationcatFactory
     */
    protected $_educationCategoryFactory;
    /**
     * @var array
     */
    protected $_globalcat;

    /**
     * Categories constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Travash\Education\Model\EducationcatFactory $educationCategoryFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Travash\Education\Model\EducationcatFactory $educationCategoryFactory
    ) {
        parent::__construct($context);
        $this->_educationCategoryFactory = $educationCategoryFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * @param $id
     * @return bool
     */
    function hasChild($id)
    {
        $educationCategoryModel = $this->_educationCategoryFactory->create();
        /* @phpstan-ignore-next-line */
        $educationCategoryColl = $educationCategoryModel->load($id);
        if ($educationCategoryColl->getEducationCatId()) {
            return true;
        }
        return false;
    }

    /**
     * @param int $parent
     * @param int $level
     */
    public function dumpTree($parent = 0, $level = 0)
    {
        $educationCategoryModel = $this->_educationCategoryFactory->create();
        /* @phpstan-ignore-next-line */
        $educationCategoryColl = $educationCategoryModel->getCollection()
            ->addFieldToFilter('is_active', ["eq" => 1]);
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        foreach ($educationCategoryColl as $cat) {
            if ($parent == $cat['parent_cat_id']) {
                $this->_globalcat['options'][$cat['education_cat_id']] =
                    str_repeat($nonEscapableNbspChar, $level * 4) . $cat['name'];
                if ($this->hasChild($cat['education_cat_id'])) {
                    $this->dumpTree($cat['education_cat_id'], $level + 1);
                }
            }
        }
    }

    /**
     * @param int $parent
     * @param int $level
     */
    public function dumpTreedesign($parent = 0, $level = 0)
    {
        $educationCategoryModel = $this->_educationCategoryFactory->create();
        /* @phpstan-ignore-next-line */
        $educationCategoryColl = $educationCategoryModel->getCollection()
            ->addFieldToFilter('is_active', ["eq" => 1]);
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        foreach ($educationCategoryColl as $cat) {
            if ($parent == $cat['parent_cat_id']) {
                $this->_globalcat['optionsdesign'][$cat['education_cat_id']] =
                    $cat['url_key'] . '*' .
                    str_repeat($nonEscapableNbspChar, $level * 4) .
                    $cat['name'];
                if ($this->hasChild($cat['education_cat_id'])) {
                    $this->dumpTreedesign($cat['education_cat_id'], $level + 1);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getOptionArraytwo()
    {
        $this->_globalcat['options'][0] = 'Create Parent';
        $val = $this->dumpTree();
        return $this->_globalcat['options'];
    }

    /**
     * @return mixed
     */
    public function getOptionArray()
    {
        $categorieModel = $this->_educationCategoryFactory->create();
        /* @phpstan-ignore-next-line */
        $categorieDetail = $categorieModel->getCollection();
        $categoryData = [];
        foreach ($categorieDetail as $detail) {
            $categoryData[$detail['education_cat_id']] = $detail['name'];
        }
        return $categoryData;
    }

    /**
     * @return mixed
     */
    public function getfrontOptionArray()
    {
        if (empty($this->_globalcat['optionsdesign'])) {
            $this->_globalcat['optionsdesign'] = [];
        }
        $val = $this->dumpTreedesign();
        return $this->_globalcat['optionsdesign'];
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['label' => $value, 'value' => $index];
        }
        return $result;
    }

    /**
     * @param $optionId
     * @return null
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
