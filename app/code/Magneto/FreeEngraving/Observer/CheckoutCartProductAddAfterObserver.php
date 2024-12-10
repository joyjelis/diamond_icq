<?php

namespace Magneto\FreeEngraving\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;

class CheckoutCartProductAddAfterObserver implements ObserverInterface {
	/**
	 * @var \Magento\Framework\View\LayoutInterface
	 */
	protected $_layout;
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;
	protected $_request;
	/**
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\View\LayoutInterface $layout
	 */
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\View\LayoutInterface $layout,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Catalog\Model\Product $productModel,
		SerializerInterface $serializer
	) {
		$this->_layout = $layout;
		$this->_storeManager = $storeManager;
		$this->_logger = $logger;
		$this->_request = $request;
		$this->_productModel = $productModel;
		$this->serializer = $serializer;
	}
	/**
	 * Add order information into GA block to render on checkout success pages
	 *
	 * @param EventObserver $observer
	 * @return void
	 */
	public function execute(EventObserver $observer) {
		$this->_logger->debug('Free Engraving Module - Checkout Cart Product Add After Observer');
		/* To Do */
		// Edit Cart - May need to remove option and readd them
		// Pre-fill remarks on product edit pages
		// Check for comparability with custom option

		/* @var \Magento\Quote\Model\Quote\Item $item */

		$item = $observer->getQuoteItem();
		$itemId = $item->getItemId();
		$this->_logger->debug('current product item -' . $itemId . '- request params : ' . json_encode($this->_request->getParams()));

		$additionalOptions = [];
		if ($additionalOption = $item->getOptionByCode('additional_options')) {
			$additionalOptions = $this->serializer->unserialize($additionalOption->getValue());
		}

		if ($this->_request->getParam('addtocart-engravingTextTypeLabel')) {
			$foneLabel = $this->_request->getParam('addtocart-engravingTextTypeLabel');
			$fontValue = $this->_request->getParam('addtocart-engravingTextType');
			if ($foneLabel != '') {
				$additionalOptions[] = [
					'label' => __($foneLabel),
					'value' => $fontValue,
				];
			}
		}

		if ($this->_request->getParam('addtocart-engravingTextLabel')) {
			$textLabel = $this->_request->getParam('addtocart-engravingTextLabel');
			$textValue = $this->_request->getParam('addtocart-engravingText');
			if ($textLabel != '') {
				$additionalOptions[] = [
					'label' => __($textLabel),
					'value' => $textValue,
				];
			}
		}
		/*
			        if($this->_request->getParam('addtocart-engravingTextTypeValue')){
			            $textLabel =  "temp_font_script_value";
			            $textValue =  $this->_request->getParam('addtocart-engravingTextTypeValue');
			            if($textLabel != ''){
			                $additionalOptions[] = [
			                    'label' => $textLabel,
			                    'value' => $textValue
			                ];
			            }
			        }
		*/

		if (count($additionalOptions) > 0) {
			$this->_logger->debug('Free Engraving Module - Additional Options Available');

			$addAdditionalOption = false;
			if ($this->_request->getParam('ringId')) {
				$this->_logger->debug('Free Engraving Module - ringId Available');
				$currentRingId = $this->_request->getParam('ringId');
				$currentProductId = $item->getProductId();
				$currentProductSKU = $item->getProduct()->getSku();
				$this->_logger->debug("Free Engraving Module - ringId : " . $currentRingId . " ==> Current SKU: " . $currentProductSKU . "Available");
				$currentRingIdFormat = 'rb-' . $currentRingId;
				if ($currentRingIdFormat == $currentProductSKU) {
					$this->_logger->debug("Free Engraving Module - ringId : " . $currentRingIdFormat . " ==> Current SKU: " . $currentProductSKU . " Matched");
					$addAdditionalOption = true;
				}
			} else {
				$addAdditionalOption = true;
			}

			if ($addAdditionalOption) {
				$this->_logger->debug("Free Engraving Module - come to add Additonal Options.");
				$prod = $this->_productModel->load($item->getProductId());
				$attr_code = 'free_engraving';
				$prod->setCustomAttribute($attr_code, 1);
				$prod->save();
				$this->_logger->debug("Free Engraving Module - come to add Additonal Options - 1");

				$item->addOption([
					'product_id' => $item->getProductId(),
					'code' => 'additional_options',
					'value' => $this->serializer->serialize($additionalOptions),
				]);
				$this->_logger->debug("Free Engraving Module - come to add Additonal Options - 2");

			}

		}
	}
}
