<?php

namespace Magneto\GlobalTel\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Registry;

/**
 * Class Color
 * @package Magneto\GlobalTel\Block
 */
class Color extends Field {
	/**
	 * @var Registry
	 */
	protected $_coreRegistry;

	/**
	 * Color constructor.
	 * @param Context $context
	 * @param Registry $coreRegistry
	 * @param array $data
	 */
	public function __construct(
		Context $context,
		Registry $coreRegistry,
		array $data = []
	) {
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context, $data);
	}

	/**
	 * @param AbstractElement $element
	 * @return string
	 */
	protected function _getElementHtml(AbstractElement $element) {
		$html = $element->getElementHtml();
		$cpPath = $this->getViewFileUrl('Magneto_GlobalTel::js');
		if (!$this->_coreRegistry->registry('colorpicker_loaded')) {
			$html .= '<script src="' . $cpPath . '/' . 'jscolor.js"></script>';
			$this->_coreRegistry->registry('colorpicker_loaded', 1);
		}
		$html .= '<script>
                var el = document.getElementById("' . $element->getHtmlId() . '");
                el.className = el.className + " jscolor{hash:true}";
            </script>';
		return $html;
	}
}