<?php

namespace Magneto\TransferMethods\Block\Adminhtml\Edit\Tab\View;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\Registry;
use Magneto\TransferMethods\Model\ResourceModel\Methods\CollectionFactory;

class Methods extends \Magento\Backend\Block\Widget\Grid\Extended {

	protected $_coreRegistry = null;

	protected $_collectionFactory;

	public function __construct(
		Context $context,
		Data $backendHelper,
		CollectionFactory $collectionFactory,
		Country $country,
		Registry $coreRegistry,
		array $data = []
	) {
		$this->country = $country;
		$this->_coreRegistry = $coreRegistry;
		$this->_collectionFactory = $collectionFactory;
		parent::__construct($context, $backendHelper, $data);
	}

	protected function _construct() {
		parent::_construct();
		$this->setId('view_transfer_methods_grid');
		$this->setUseAjax(true);
		$this->setDefaultSort('created_at', 'desc');
		$this->setSortable(true);
		$this->setPagerVisibility(true);
		$this->setFilterVisibility(true);
	}

	protected function _prepareCollection() {
		$collection = $this->_collectionFactory->create()
			->addFieldToFilter('customer_id', $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn(
			'method_id',
			['header' => __('ID'), 'index' => 'method_id', 'type' => 'number', 'width' => '100px']
		);
		$this->addColumn(
			'bank_name',
			[
				'header' => __('Bank Name'),
				'index' => 'bank_name',
			]
		);
		$this->addColumn(
			'account_name',
			[
				'header' => __('Account Name'),
				'index' => 'account_name',
			]
		);
		$this->addColumn(
			'account_no',
			[
				'header' => __('Account No'),
				'index' => 'account_no',
			]
		);
		$this->addColumn(
			'swift_code',
			[
				'header' => __('Swift Code'),
				'index' => 'swift_code',
			]
		);

		$country = [];
		foreach ($this->country->toOptionArray() as $key => $value) {
			$country[$value['value']] = $value['label'];
		}

		$this->addColumn(
			'country',
			[
				'header' => __('Country'),
				'type' => 'options',
				'index' => 'country',
				'options' => $country,
				'renderer' => \Magneto\TransferMethods\Block\Adminhtml\Grid\Column\Country::class,
			]
		);

		$this->addColumn('created_at', array('header' => __('Created At'), 'align' => 'left', 'width' => '120px', 'type' => 'datetime', 'index' => 'created_at'));

		$this->addColumn('updated_at', array('header' => __('Updated At'), 'align' => 'left', 'width' => '120px', 'type' => 'datetime', 'index' => 'updated_at'));

		$this->addColumn(
			'action',
			[
				'header' => __('Action'),
				'sortable' => false,
				'filter' => false,
				'renderer' => \Magneto\TransferMethods\Block\Adminhtml\Grid\Column\Action::class,
				'header_css_class' => 'col-action',
				'column_css_class' => 'col-action',
			]
		);

		return parent::_prepareColumns();
	}

	public function getHeadersVisibility() {
		return $this->getCollection()->getSize() >= 0;
	}

	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', ['id' => $row->getMethodId()]);
	}

	public function getGridUrl() {
		return $this->getUrl('*/*/load', array('_current' => true));
	}
}