<?php

namespace Magneto\FreeEngraving\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper {

	protected $eavConfig;
	const XML_IS_ENABLE = "engraving_action/general/enabled";
	const RING_COOKIE_NAME = 'ringEngravingCookie_';
	const COOKIE_DURATION = 86400; // lifetime in seconds (24 hour)
	protected $_cookieManager;
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
		\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
		\Magento\Eav\Model\Config $eavConfig
	) {
		$this->eavConfig = $eavConfig;
		$this->_cookieManager = $cookieManager;
		$this->_cookieMetadataFactory = $cookieMetadataFactory;
		parent::__construct($context);
	}
	public function isEnabled() {
		return $this->getConfig(self::XML_IS_ENABLE);
	}
	public function getConfig($config) {
		return $this->scopeConfig->getValue(
			$config,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	public function getEngravingFontOptions() {
		$optionsExists = [];
		$attribute = $this->eavConfig->getAttribute('catalog_product', 'engraving_font');
		$options = $attribute->getSource()->getAllOptions();

		foreach ($options as $option) {
			//print_r($option);exit;
			$currentLabel = $option['label']->getText();
			$currentLabelValue = $option['value'];
			$currentDataCharacterLimit = $option['data-character-limit'];
			$isChineseFont = $option['isChineseFont'];
			$optionsExists[] = ['label' => $currentLabel, 'value' => $currentLabelValue, 'data-character-limit' => $currentDataCharacterLimit, 'isChineseFont' => $isChineseFont];
		}
		return $optionsExists;
	}
	public function generateRingEngravingCookie($id, $data) {
		/*
			        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/AddRingEngravingPlugin.log');
			        $logger = new \Zend_Log();
			        $logger->addWriter($writer);
			        $logger->info('come to generateRingEngravingCookie');
		*/
		$ringengravingcookie = self::RING_COOKIE_NAME . $id;
		//$logger->info('come to generateRingEngravingCookie ID '.$ringengravingcookie);
		$metadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()
			->setDuration(self::COOKIE_DURATION)
			->setPath('/');
		$ringinfo = json_encode($data);
		//  $logger->info('come to generateRingEngravingCookie ringinfo '. $ringinfo);
		$this->_cookieManager->setPublicCookie($ringengravingcookie, $ringinfo, $metadata);
		// $logger->info('come to generateRingEngravingCookie ringinfo updated');
	}
	public function getRingEngravingCookieData($id) {
		/*
			        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/AddRingEngravingPlugin.log');
			        $logger = new \Zend_Log();
			        $logger->addWriter($writer);
			        $logger->info('come to getRingEngravingCookieData');
		*/
		$ringsettingcookie = [];
		$ringengravingcookie = self::RING_COOKIE_NAME . $id;
		//$logger->info('come to getRingEngravingCookieData ID '.$ringengravingcookie);
		$ringsettingcookie = json_decode($this->_cookieManager->getCookie($ringengravingcookie));
		$ringsettingcookie = (array) $ringsettingcookie;
		return $ringsettingcookie;
	}
}
