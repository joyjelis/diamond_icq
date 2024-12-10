<?php
namespace Magneto\FreeEngraving\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class StatusOptions extends AbstractSource {
	/**
	 * Get all options
	 *
	 * @return array
	 */
	public function getAllOptions() {
		if (null === $this->_options) {
			$this->_options = [
				['label' => __('Arial'), 'value' => "Arial", "data-character-limit" => "12", "isChineseFont" => "0"],
				['label' => __('Times New Roman'), 'value' => "Times_New_Roman", "data-character-limit" => "12", "isChineseFont" => "0"],
				['label' => __('Times New Roman - Italic'), 'value' => "Times_New_Roman_Italic", "data-character-limit" => "12", "isChineseFont" => "0"],
				['label' => __('PMingLiU'), 'value' => "PMingLiU", "data-character-limit" => "6", "isChineseFont" => "1"],
			];
		}
		return $this->_options;
	}
}
