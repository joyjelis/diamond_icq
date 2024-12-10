<?php
namespace Magneto\Checkout\Plugin\Block\Checkout;
/**
 * Class AttributeMerger
 * @package RH\Helloworld\Plugin\Block\Checkout\AttributeMerger
 */
class AttributeMerger {
	/**
	 * @param \Magento\Checkout\Block\Checkout\AttributeMerger $subject
	 * @param $result
	 * @return mixed
	 */
	public function afterMerge(
		\Magento\Checkout\Block\Checkout\AttributeMerger $subject,
		$result
	) {
		$result['firstname']['placeholder'] = __('First Name*');
		$result['lastname']['placeholder'] = __('Last Name*');
		$result['street']['children'][0]['placeholder'] = __('Address Line') . ' ' . __("1*");
		$result['street']['children'][1]['placeholder'] = __('Address Line') . ' ' . __("2");
		$result['city']['placeholder'] = __('City*');
		$result['postcode']['placeholder'] = __('Zip/Postal Code*');
		$result['telephone']['placeholder'] = __('Phone Number*');
		return $result;
	}
}