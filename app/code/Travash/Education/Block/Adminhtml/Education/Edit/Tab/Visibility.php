<?php
namespace Travash\Education\Block\Adminhtml\Education\Edit\Tab;

/**
 * Class Visibility
 * @package Travash\Education\Block\Adminhtml\Education\Edit\Tab
 */
class Visibility extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Travash\Education\Model\Status
     */
    protected $_status;
    /**
     * @var \Travash\Education\Model\Categories
     */
    protected $_educationCategory;
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Visibility constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\Registry $registry
     * @param \Travash\Education\Model\Status $status
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Travash\Education\Model\Categories $educationCategory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Registry $registry,
        \Travash\Education\Model\Status $status,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Travash\Education\Model\Categories $educationCategory
    ) {
        $this->systemStore = $systemStore;
        $this->_status = $status;
        $this->_educationCategory = $educationCategory;
        parent::__construct($context, $registry, $formFactory);
    }

    /**
     * @return mixed
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' =>
                    [
                        'html_id_prefix' => 'page_additional_'
                    ]
            ]
        );
        $model = $this->_coreRegistry->registry('education');
        $isElementDisabled = false;
        $fieldSet = $form->addFieldset(
            'Additional_fieldset',
            [
                'legend' => __('Visibility'),
                'class' => 'fieldset-wide',
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldSet->addField(
            'education_category',
            'multiselect',
            [
                'name' => 'education_category[]',
                'label' => __('Categories'),
                'title' => __('Categories'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'values' => $this->_educationCategory->toOptionArray()
            ]
        );

        $fieldSet->addField(
            'store_id',
            'multiselect',
            [
                'name' => 'store_id[]',
                'label' => __('Store'),
                'title' => __('Store'),
                'values' => $this->systemStore
                    ->getStoreValuesForForm(false, true),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldSet->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'options' => $this->_status->getOptionArray(),
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
        return __('Visibility');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return __('Visibility');
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
