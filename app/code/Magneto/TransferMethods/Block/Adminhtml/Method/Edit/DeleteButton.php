<?php

declare (strict_types = 1);

namespace Magneto\TransferMethods\Block\Adminhtml\Method\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface {

	/**
	 * Delete Sell record
	 * @return array
	 */
	public function getButtonData() {
		$data = [];
		if ($this->getModelId()) {
			$data = [
				'label' => __('Delete'),
				'class' => 'delete',
				'on_click' => 'deleteConfirm(\'' . __(
					'Are you sure you want to do this?'
				) . '\', \'' . $this->getDeleteUrl() . '\')',
				'sort_order' => 20,
			];
		}
		return $data;
	}

	/**
	 * Get URL for delete button
	 *
	 * @return string
	 */
	public function getDeleteUrl() {
		return $this->getUrl('*/*/delete', ['method_id' => $this->getModelId()]);
	}
}
