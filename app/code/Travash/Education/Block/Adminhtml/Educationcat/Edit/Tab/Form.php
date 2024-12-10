<?php
namespace Travash\Education\Block\Adminhtml\Educationcat\Edit\Tab;

/**
 * Class Form
 * @package Travash\Education\Block\Adminhtml\Educationcat\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    /**
     * @var \Travash\Education\Model\Status
     */
    protected $_status;
    /**
     * @var \Travash\Education\Model\Categories
     */
    protected $_categories;
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Travash\Education\Model\Categories $categories
     * @param \Travash\Education\Model\Status $status
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Travash\Education\Model\Categories $categories,
        \Travash\Education\Model\Status $status
    ) {
        $this->_localeDate = $context->getLocaleDate();
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_categories = $categories;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory);
    }

    /**
     * @return mixed
     */
    protected function _prepareLayout()
    {
        $layout = $this->getLayout()->getBlock(
            'page.title'
        );
        if (method_exists($layout, 'setPageTitle')) {
            $layout->setPageTitle(
                $this->getPageTitle()
            );
        }
        return $layout;
    }

    /**
     * @return mixed
     */
    protected function _prepareForm()
    {
        $model = $this->getEducationcat();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldSet = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Category Information')
            ]
        );

        if ($model->getId()) {
            $fieldSet->addField(
                'education_cat_id',
                'hidden',
                [
                    'name' => 'education_cat_id'
                ]
            );
        }

        $fieldSet->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'class' => 'required-entry',
            ]
        );

        $wysiwygConfig = $this->_wysiwygConfig
            ->getConfig(
                [
                    'tab_id' => $this->getTabId()
                ]
            );
        $fieldSet->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => false,
                'class' => 'required-entry',
                'config' => $wysiwygConfig
            ]
        );

        $fieldSet->addField(
            'parent_cat_id',
            'select',
            [
                'name' => 'parent_cat_id',
                'label' => __('Parent Category'),
                'title' => __('Parent Category'),
                'required' => true,
                'class' => 'required-entry',
                'options' => $this->_categories->getOptionArraytwo()
            ]
        );

        $fieldSet->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
                'class' => 'required-entry',
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getEducationcat()
    {
        return $this->_coreRegistry->registry('educationcat');
    }

    /**
     * @return mixed
     */
    public function getPageTitle()
    {
        return $this->getEducationcat()->getId() ? __(
            "Edit Category '%1'",
            $this->escapeHtml($this->getEducationcat()->getName())
        ) : __('New Category');
    }

    /**
     * @return mixed
     */
    public function getTabLabel()
    {
        return __('Category Information');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return __('Category Information');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
