<?php
namespace Travash\Education\Block\Adminhtml\Educationcat\Edit\Tab;

/**
 * Class Visibility
 * @package Travash\Education\Block\Adminhtml\Educationcat\Edit\Tab
 */
class Visibility extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Travash\Education\Model\Status
     */
    protected $_status;
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * Visibility constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\System\Store $_systemStore
     * @param \Travash\Education\Model\Status $status
     * @param \Magento\Framework\Data\FormFactory $formFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\System\Store $_systemStore,
        \Travash\Education\Model\Status $status,
        \Magento\Framework\Data\FormFactory $formFactory
    ) {
        $this->_status = $status;
        $this->systemStore = $_systemStore;
        parent::__construct($context, $registry, $formFactory);
    }

    /**
     * @return mixed
     */
    protected $_formFactory;
    protected $_coreRegistry;

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create([
            'data' => ['html_id_prefix' => 'page_additional_']
        ]);
        $model = $this->_coreRegistry->registry('educationcat');
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
