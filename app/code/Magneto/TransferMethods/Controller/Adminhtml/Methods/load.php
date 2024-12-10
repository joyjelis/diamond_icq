<?php

namespace Magneto\TransferMethods\Controller\Adminhtml\Methods;

class Load extends \Magento\Customer\Controller\Adminhtml\Index {

	public function execute() {
		$this->initCurrentCustomer();
		$resultLayout = $this->resultLayoutFactory->create();
		return $resultLayout;
	}
}
