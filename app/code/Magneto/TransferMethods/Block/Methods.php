<?php

namespace Magneto\TransferMethods\Block;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template\Context;
use Magneto\TransferMethods\Helper\Data as Helper;

class Methods extends \Magento\Framework\View\Element\Template {

	CONST PAGE_LIMIT = array(4, 8, 12, 16, 20);

	CONST MASKING_LIMIT = -4;

	protected $_page = 1;

	protected $collection;

	public function __construct(
		Context $context,
		RequestInterface $request,
		Helper $helper,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->helper = $helper;
	}

	protected function _prepareLayout() {
		parent::_prepareLayout();
		$this->pageConfig->getTitle()->set(__('Transfer Method'));
		$page_size = array_combine(self::PAGE_LIMIT, self::PAGE_LIMIT);
		$collection = $this->getMethods();
		if ($collection) {
			$blockName = 'transfer.methods.history.pager_' . rand();
			$pager = $this->getLayout()->createBlock(
				\Magento\Theme\Block\Html\Pager::class,
				$blockName
			)->setAvailableLimit($page_size)->setShowPerPage(true)->setCollection($collection);
			$this->setChild('pager', $pager);
			$collection->load();
		}

		return $this;
	}

	public function MaskingNumber($number, $maskingCharacter = '*') {
		return str_repeat($maskingCharacter, strlen($number)) .
		substr($number, self::MASKING_LIMIT);
		// return substr($number, 0, ) . str_repeat($maskingCharacter, strlen($number) - 8) . substr($number, -4);
	}

	public function getCountryname($countryCode) {
		return $this->helper->getCountryname($countryCode);
	}

	public function Encrypt($data) {
		return base64_encode(json_encode($data));
	}

	public function getMethods() {
		$this->setPage();
		if ($this->collection) {
			return $this->collection;
		}

		if ($collection = $this->helper->getMethods($this->_page)) {
			$this->collection = $collection;
			return $collection;
		}

		return false;
	}

	public function getCountries($defValue = null, $name = 'country', $id = 'country', $title = 'Country') {
		return $this->helper->getCountries($defValue, $name, $id, $title);
	}

	public function setPage() {
		$page = $this->getRequest()->getParam('p');
		if ($page) {
			$this->_page = $page;
		}
	}

	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}

	public function getJsConfig() {
		$this->jsconfig['save_url'] = $this->getUrl('*/*/save');
		return json_encode($this->jsconfig);
	}
}
