<?php
/**
 * @author Magneto Team
 * @copyright Copyright (c) 2021 Magneto (https://www.magnetoitsolutions.com)
 * @package Magneto_WireTransfer
 */

namespace Magneto\WireTransfer\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Registry;

class Editor extends \Magento\Config\Block\System\Config\Form\Field {
/**
 * @var  Registry
 */
	protected $_coreRegistry;
/**
 * @param Context    $context
 * @param WysiwygConfig $wysiwygConfig
 * @param array      $data
 */

	public function __construct(
		Context $context,
		WysiwygConfig $wysiwygConfig,
		array $data = []
	) {
		$this->_wysiwygConfig = $wysiwygConfig;
		parent::__construct($context, $data);
	}
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
		// set wysiwyg for element
		$element->setWysiwyg(true);
		$config = $this->_wysiwygConfig->getConfig($element);
		$config['add_variables'] = 0;
		$config['add_widgets'] = 0;
		$config['plugins'] = [];
		$config['add_images'] = 0;

		// set configuration values
		$element->setConfig($config);
		return parent::_getElementHtml($element);
	}
}