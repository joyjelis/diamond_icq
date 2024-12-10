<?php
namespace Travash\Education\Block\Adminhtml\Educationcat;

/**
 * Class Grid
 * @package Travash\Education\Block\Adminhtml\Educationcat
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Travash\Education\Model\EducationcatFactory
     */
    protected $_educationCategoryFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Travash\Education\Model\Status
     */
    protected $_status;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Travash\Education\Model\Status $status
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Travash\Education\Model\EducationcatFactory $educationCategoryFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Travash\Education\Model\Status $status,
        \Magento\Backend\Helper\Data $backendHelper,
        \Travash\Education\Model\EducationcatFactory $educationCategoryFactory
    ) {
        $this->_educationCategoryFactory = $educationCategoryFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_status = $status;
        parent::__construct($context, $backendHelper);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('educationcatGrid');
        $this->setDefaultSort('education_cat_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }

    /**
     * @return mixed
     */
    protected function _prepareCollection()
    {
        /* @phpstan-ignore-next-line */
        $collection = $this->_educationCategoryFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'education_cat_id',
            [
                'header' => __('Category ID'),
                'type' => 'number',
                'index' => 'education_cat_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'index' => 'sort_order',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'class' => 'xxx',
                'width' => '50px',
                'type' => 'options',
                'options' => ['1' => 'Enabled', '2' => 'Disabled']
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'index' => 'is_active',
                'type' => 'action',
                'getter' => 'getId',
                'class' => 'xxx',
                'width' => '20px',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                        ],
                        'field' => 'education_cat_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('education_cat_id');
        $this->getMassactionBlock()->setFormFieldName('educationcat');
        if (method_exists($this->getMassactionBlock(), 'addItem')) {
            $this->getMassactionBlock()->addItem('delete', [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/massDelete', ['' => '']),
                'confirm' => __('Are you sure?')
            ]);
        }
        $statuses = $this->_status->getOptionArray();
        array_unshift($statuses, ['label' => '', 'value' => '']);
        if (method_exists($this->getMassactionBlock(), 'addItem')) {
            $this->getMassactionBlock()->addItem('status', [
                'label' => __('Change status'),
                'url' => $this->getUrl('*/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]);
        }

        return $this;
    }

    /**
     * @param $row
     * @return mixed
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['education_cat_id' => $row->getId()]);
    }
}
