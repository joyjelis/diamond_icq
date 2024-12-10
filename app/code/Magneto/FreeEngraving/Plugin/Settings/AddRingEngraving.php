<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_FreeEngraving
 */

namespace Magneto\FreeEngraving\Plugin\Settings;

use Gemfind\Ringbuilder\Controller\Settings\Adddiamond as GemFindAddDiamond;

class AddRingEngraving {

	public function __construct(
		\Magneto\FreeEngraving\Helper\Data $moduleHelper
	) {
		$this->moduleHelper = $moduleHelper;
	}

	public function afterExecute(GemFindAddDiamond $subject, $result) {

		$data = $subject->getRequest()->getParams();

		if (!empty($data)
			&& isset($data['addtocart-engravingTextType']) && trim($data['addtocart-engravingTextType']) != ''
			&& isset($data['addtocart-engravingText']) && trim($data['addtocart-engravingText']) != ''
		) {
			$ringengravingcookie = "ringEngravingCookie_" . $data['id'];
			//echo "<pre>";print_r($data);
			$ringinfo = $jsonData = [];
			$ringinfo['id'] = $data['id'];
			$ringinfo['addtocart-engravingTextTypeLabel'] = $data['addtocart-engravingTextTypeLabel'];
			$ringinfo['addtocart-engravingTextType'] = $data['addtocart-engravingTextType'];
			$ringinfo['addtocart-engravingTextLabel'] = $data['addtocart-engravingTextLabel'];
			$ringinfo['addtocart-engravingText'] = $data['addtocart-engravingText'];

			if (!empty($ringinfo)) {
				$this->moduleHelper->generateRingEngravingCookie($data['id'], $ringinfo);
			}
			// exit;

		}
		return $result;

	}
}