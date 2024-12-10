<?php
namespace Travash\Education\Model;

/**
 * Class EducationcatFactory
 * @package Travash\Education\Model
 */
class EducationcatFactory extends \Magento\Framework\View\Element\Template implements \Magento\Framework\Option\ArrayInterface
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
     * @var array
     */
    protected $_globalCategory;
    /**
     * @var EducationcatFactory
     */
    protected $_educationCategoryFactory;

     /**
      * Educationcategory constructor.
      * @param \Magento\Framework\View\Element\Template\Context $context
      * @param EducationcatFactory $educationCategoryFactory
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
        foreach (self::getOptionArray() as $index => $values) {
            $result[] = ['value' => $index, 'label' => $values];
        }
        return $result;
    }

   

    /**
     * @param $id
     * @return bool
     */
    public function hasChild($id)
    {
        $educationModel = $this->_educationCategoryFactory->create();
        /* @phpstan-ignore-next-line */
        $educationCollection = $educationModel->load($id);
        if ($educationCollection->getEducationId()) {
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
        $educationModel = $this->_educationCategoryFactory->create();
        /* @phpstan-ignore-next-line */
        $educationCollection = $educationModel->getCollection()
            ->addFieldToFilter('is_active', ["eq" => 1]);
        $non = html_entity_decode(
            '&#160;',
            ENT_NOQUOTES,
            'UTF-8'
        );
        foreach ($educationCollection as $category) {
            if ($parent == $category['parent_cat_id']) {
                $this->_globalCategory['options']
                [$category['education_cat_id']] =
                    str_repeat(
                        $non,
                        $level * 4
                    ) . $category['name'];
                if ($this->hasChild($category['education_cat_id'])) {
                    $this->dumpTree($category['education_cat_id'], $level + 1);
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
        $educationModel = $this->_educationCategoryFactory->create();
        /* @phpstan-ignore-next-line */
        $educationCollection = $educationModel->getCollection()
            ->addFieldToFilter('is_active', ["eq" => 1]);
        $non = html_entity_decode(
            '&#160;',
            ENT_NOQUOTES,
            'UTF-8'
        );
        foreach ($educationCollection as $category) {
            if ($parent == $category['parent_cat_id']) {
                $this->_globalCategory['optionsdesign']
                [$category['education_cat_id']] =
                    $category['url_key'] .
                    '*' .
                    str_repeat(
                        $non,
                        $level * 4
                    ) .
                    $category['name'];
                if ($this->hasChild($category['education_cat_id'])) {
                    $this->dumpTreedesign($category['education_cat_id'], $level + 1);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getOptionArraytwo()
    {
        $this->_globalCategory['options'][0] = 'Create Parent';
        return $this->_globalCategory['options'];
    }

    /**
     * @return mixed
     */
    public function getOptionArray()
    {
        if (!isset($this->_globalCategory['options'])) {
            $this->_globalCategory['options'] = [];
        }
        return $this->_globalCategory['options'];
    }

    /**
     * @return mixed
     */
    public function getfrontOptionArray()
    {
        if (empty($this->_globalCategory['optionsdesign'])) {
            $this->_globalCategory['optionsdesign'] = [];
        }
        return $this->_globalCategory['optionsdesign'];
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $values) {
            $result[] = ['label' => $values, 'value' => $index];
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
