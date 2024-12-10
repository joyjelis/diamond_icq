<?php
namespace Travash\Education\Block\Adminhtml\Education\Edit\Tab;

/**
 * Class Metasection
 * @package Travash\Education\Block\Adminhtml\Education\Edit\Tab
 */
class Metasection extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;
    
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Metasection constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory
    ) {
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory);
    }


    /**
     * @return mixed
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'html_id_prefix' => 'page_additional_'
                ]
            ]
        );
        $model = $this->_coreRegistry->registry('education');
        $isElementDisabled = false;
        $fieldSet = $form->addFieldset(
            'Additional_fieldset',
            ['legend' => __('Search Engine Optimization'),
                'class' => 'fieldset-wide',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldSet->addField(
            'meta_title',
            'text',
            [
                'name' => 'meta_title',
                'label' => __('Page Title'),
                'title' => __('Page Title'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldSet->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldSet->addField(
            'meta_keywords',
            'text',
            [
                'name' => 'meta_keywords',
                'label' => __('Meta Keywords'),
                'title' => __('Meta Keywords'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldSet->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Meta Description'),
                'title' => __('Meta Description'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldSet->addField(
            'schema_script',
            'textarea',
            [
                'name' => 'schema_script',
                'label' => __('Schema Script'),
                'title' => __('Schema Script'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getTabLabel()
    {
        return __('Search Engine Optimization');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return __('Search Engine Optimization');
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

    /**
     * @param $resourceId
     * @return mixed
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
