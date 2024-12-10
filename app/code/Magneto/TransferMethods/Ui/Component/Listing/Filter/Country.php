<?php

namespace Magneto\TransferMethods\Ui\Component\Listing\Filter;

use Magento\Directory\Model\Config\Source\Country As CountryOptions;
use Magento\Framework\Data\OptionSourceInterface;

class Country implements OptionSourceInterface {

	public function __construct(CountryOptions $country) {
		$this->country = $country;
	}

	public function toOptionArray() {
		return $this->country->toOptionArray();
	}

	public function getOptionsArray() {
		return $this->country->toOptionArray();
	}
}
