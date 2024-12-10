<?php
declare(strict_types=1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Travash\Customization\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Canonical
 * @package Travash\Customization\Helper
 */
class Canonical extends AbstractHelper
{
	/**
	 * @var StoreManagerInterface
	 */
	protected $_storeManager;
	
	/**
	 * Canonical constructor.
	 * @param Context $context
	 * @param StoreManagerInterface $storeManager
	 */
	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager
	) {
		$this->_storeManager = $storeManager;
		parent::__construct($context);
	}
	
	/**
	 * This method is used in XML layout.
	 * @return string
	 */
	public function getCanonicalTag(): string
	{
		$storeUrl = $this->_storeManager->getStore()->getCurrentUrl(false);
		$storeUrl = rtrim($storeUrl, '/');
		return $this->createLink(
			$storeUrl
		);
	}
	
	/**
	 * @param $url
	 * @return string
	 */
	protected function createLink($url): string
	{
		return '<link rel="canonical" href="' . $url . '" />';
	}
}