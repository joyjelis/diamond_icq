<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_AjaxProductsLoad
 */

namespace Magneto\AjaxProductsLoad\Controller\Product;

use Magneto\AjaxProductsLoad\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action {

	/**
	 * Result Factory
	 *
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * _Construct
	 *
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param Data $helper
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		Data $helper
	) {
		$this->resultJsonFactory = $resultJsonFactory;
		$this->helper = $helper;
		parent::__construct($context);
	}

	/**
	 * Execute
	 *
	 * @return \Magento\Framework\Controller\Result\JsonFactory
	 */
	public function execute() {
		$result = $this->resultJsonFactory->create();
		$currentactive = $this->getRequest()->getPost('currentactive');
		$currenthorizontalactive = $this->getRequest()->getPost('currenthorizontalactive');
		$limit = $this->getRequest()->getPost('limit');

		$categoryIds = [];
		if (!empty($currentactive)) {
			array_push($categoryIds, $currentactive);
		}

		if (!empty($currenthorizontalactive)) {
			array_push($categoryIds, $currenthorizontalactive);
		}

		if (!empty($categoryIds)) {
			$html = $this->helper->getProducts($categoryIds, $limit);
			$size = $this->helper->getCollectionSize();
			$result->setData(['success' => true, 'html' => $html, 'size' => $size]);
		} else {
			$result->setData(['success' => false]);
		}

		return $result;

	}
}