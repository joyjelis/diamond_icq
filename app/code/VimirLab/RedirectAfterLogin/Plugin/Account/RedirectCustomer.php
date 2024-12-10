<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace VimirLab\RedirectAfterLogin\Plugin\Account;

use Magento\Store\Model\StoreManagerInterface;

class RedirectCustomer
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * RedirectCustomer Constructor
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }
    
    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        $result
    ) {
        $customUrl = $this->_storeManager->getStore()->getBaseUrl();
        $result->setPath($customUrl);
        return $result;
    }
}
