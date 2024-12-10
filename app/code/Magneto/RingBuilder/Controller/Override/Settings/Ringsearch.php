<?php

namespace Magneto\RingBuilder\Controller\Override\Settings;

use Gemfind\Ringbuilder\Helper\Data as Helper;

class Ringsearch extends \Gemfind\Ringbuilder\Controller\Settings\Ringsearch {

	public function execute() {
		if (!$this->helper->isGemfindEnabled()) {
			$this->messageManager->addError(__("Please enable this Extension, go to configuration."));
			$this->_redirect('/');
		}

		if (!$this->helper->getUsername()) {
			$this->messageManager->addError(__("Please add Gemfind UserID in Extension Configuration"));
			$this->_redirect('/');
		}

		$data = $this->getRequest()->getPostValue();

		if ($data) {
			if ($data['submitby'] != 'ajax') {
				$this->_redirect('ringbuilder/settings');
			}
			$data = $this->getRequest()->getPostValue();
			$data = $this->Transformdata($data);
			$this->getRequest()->setPostValue($data);
			$result = $this->resultJsonFactory->create();
			$resultPage = $this->resultPageFactory->create();
			$block = $resultPage->getLayout()
				->createBlock('Gemfind\Ringbuilder\Block\Settings\Search\Result')
				->setTemplate('Gemfind_Ringbuilder::settings/result.phtml')
				->setData('data', $data)
				->toHtml();
			$result->setData(['output' => $block]);
			return $result;
		}

		$this->_redirect('ringbuilder/settings');
	}

	public function Transformdata($data) {
		if (isset($data['price']) && is_array($data['price'])) {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$helper = $objectManager->create('Magneto\RingBuilder\ViewModel\PriceHelper');
			foreach ($data['price'] as $key => $value) {
				$data['price'][$key] = $helper->convertToUSDCurrency($value, 0);
			}
		}

		return $data;
	}
}
