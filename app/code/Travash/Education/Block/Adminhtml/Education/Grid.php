<?php
namespace Travash\Education\Block\Adminhtml\Education;

/**
 * Class Grid
 * @package Travash\Education\Block\Adminhtml\Education
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Travash\Education\Model\EducationFactory
     */
    protected $_educationFactory;
    /**
     * @var \Travash\Education\Model\Status
     */
    protected $_status;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Travash\Education\Model\Status $status
     * @param \Travash\Education\Model\EducationFactory $educationFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Travash\Education\Model\Status $status,
        \Travash\Education\Model\EducationFactory $educationFactory
    ) {
        $this->_educationFactory = $educationFactory;
        $this->_status = $status;
        parent::__construct($context, $backendHelper);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('educationGrid');
        $this->setDefaultSort('education_id');
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
        $collection = $this->_educationFactory->create()->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'education_id',
            [
                'header' => __('Education ID'),
                'type' => 'number',
                'index' => 'education_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'index' => 'sort_order',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
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
                'width' => '20px',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                        ],
                        'field' => 'education_id'
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
        $this->setMassactionIdField('education_id');
        $this->getMassactionBlock()->setFormFieldName('education');
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
        return $this->getUrl('*/*/edit', ['education_id' => $row->getId()]);
    }
}
