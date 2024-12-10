<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magneto\TransferMethods\Block\Adminhtml\Grid\Column;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
	/**
	 * @var Action\UrlBuilder
	 */
	protected $actionUrlBuilder;

	/**
	 * @param \Magento\Backend\Block\Context $context
	 * @param Action\UrlBuilder $actionUrlBuilder
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Context $context,
		\Magento\Framework\UrlInterface $frontendUrlBuilder,
		array $data = []
	) {
		$this->frontendUrlBuilder = $frontendUrlBuilder;
		parent::__construct($context, $data);
	}

	/**
	 * Render action
	 *
	 * @param \Magento\Framework\DataObject $row
	 * @return string
	 */
	public function render(\Magento\Framework\DataObject $row) {
		$href = $this->frontendUrlBuilder->getUrl(
			'*/*/edit',
			[
				'method_id' => $row->getData('method_id'),
			]
		);

		return '<a href="' . $href . '" target="_blank">' . __('Edit') . '</a>';
	}
}
