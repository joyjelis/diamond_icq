<?php

namespace Magneto\RingBuilder\Plugin;

class Email {

	public function aroundGetEmailTemplate(\Gemfind\Ringbuilder\Helper\Data $subject, $proceed, $templateId) {
		$templateId = str_replace("gemfinddiamondsearch", "gemfindringbuilder", $templateId);
		return $proceed($templateId);
	}
}