<?php
namespace Travash\Education\Block\Adminhtml\Education\Edit\Tab;

use Travash\Education\Model\Status;

/**
 * Class Form
 * @package Travash\Education\Block\Adminhtml\Education\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    /**
     * @var Status
     */
    protected $_status;
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param Status $status
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Travash\Education\Model\Status $status
    ) {
        $this->_localeDate = $context->getLocaleDate();
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory);
    }

    /**
     *
     */
    protected function _prepareLayout()
    {
        /* @phpstan-ignore-next-line */
        $this->getLayout()
            ->getBlock('page.title')
            ->setPageTitle(
                $this->getPageTitle()
            );
    }

    /**
     * @return mixed
     */
    protected function _prepareForm()
    {
        $model = $this->getEducation();
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Education Information')
            ]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'education_id',
                'hidden',
                [
                    'name' => 'education_id'
                ]
            );
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'class' => 'required-entry',
            ]
        );

        $wysiwygConfig = $this->_wysiwygConfig->getConfig();
        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => true,
                'class' => 'required-entry',
                'config' => $wysiwygConfig
            ]
        );

        $fieldset->addField(
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

        $fieldset->addField(
            'is_featured',
            'select',
            [
                'name' => 'is_featured',
                'label' => __('Featured Education'),
                'title' => __('Featured Education'),
                'required' => false,
                'options' => ['1' => 'Yes', '0' => 'No'],
                'class' => 'required-entry',
            ]
        );
        
        $fieldset->addField(
            'featured_img',
            'image',
            [
                'name' => 'featured_img',
                'label' => __('Featured Image'),
                'title' => __('Featured Image'),
                'required' => false,
                'note' => 'Allow image type: jpg, jpeg, png',
                'class' => 'required-entry required-file',
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getEducation()
    {
        return $this->_coreRegistry->registry('education');
    }

    /**
     * @return mixed
     */
    public function getPageTitle()
    {
        return $this->getEducation()->getId() ? __(
            "Edit Education '%1'",
            $this->escapeHtml($this->getEducation()->getTitle())
        ) : __('New Education');
    }

    /**
     * @return mixed
     */
    public function getTabLabel()
    {
        return __('Education Information');
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
