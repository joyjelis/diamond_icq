<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magneto\AutofillContactUs\ViewModel\Override;

use Magento\Contact\Helper\Data;
use Magento\Customer\Model\Session;

/**
 * Provides the user data to fill the form.
 */
class UserDataProvider extends \Magento\Contact\ViewModel\UserDataProvider {

	/**
	 * @var Data
	 */
	private $helper;

	/**
	 * UserDataProvider constructor.
	 * @param Data $helper
	 */
	public function __construct(
		Data $helper,
		Session $customerSession
	) {
		$this->helper = $helper;
		$this->_customerSession = $customerSession;
		parent::__construct($helper);
	}

	/**
	 * Get user first name
	 *
	 * @return string
	 */
	public function getFirstName() {
		if (!$this->_customerSession->isLoggedIn()) {
			return '';
		}

		/**
		 * @var \Magento\Customer\Api\Data\CustomerInterface $customer
		 */
		$customer = $this->_customerSession->getCustomerDataObject();

		return $this->helper->getPostValue('name') ?: $customer->getFirstname();
	}

	/**
	 * Get user last name
	 *
	 * @return string
	 */
	public function getLastName() {
		if (!$this->_customerSession->isLoggedIn()) {
			return '';
		}

		/**
		 * @var \Magento\Customer\Api\Data\CustomerInterface $customer
		 */
		$customer = $this->_customerSession->getCustomerDataObject();

		return $this->helper->getPostValue('lastname') ?: $customer->getLastname();
	}
}
