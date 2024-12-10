<?php

namespace Magneto\RingBuilder\Controller\Override\Diamond;

use Gemfind\Ringbuilder\Helper\Data as Helper;

class Diamondsearch extends \Gemfind\Ringbuilder\Controller\Diamond\Diamondsearch {
	/**
	 * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
	 */
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
				$this->_redirect('ringbuilder/diamond');
			}

			$data = $this->getRequest()->getPostValue();
			$data = $this->Transformdata($data);
			$this->getRequest()->setPostValue($data);
			$result = $this->resultJsonFactory->create();
			$resultPage = $this->resultPageFactory->create();
			$block = $resultPage->getLayout()
				->createBlock('Gemfind\Ringbuilder\Block\Diamond\Search\Result')
				->setTemplate('Gemfind_Ringbuilder::diamond/result.phtml')
				->setData('data', $data)
				->toHtml();
			$result->setData(['output' => $block]);
			return $result;
		}
		$this->_redirect('ringbuilder/diamond');
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
