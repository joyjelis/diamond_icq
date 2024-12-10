<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Customer\Sell\Block\Quote;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;

class Success extends Template
{
    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * Quote Success Constructor
     * @param Template\Context $context
     * @param CustomerSession $customerSession
     * @param array $data = []
     */
    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
    }

    /**
     * Get QuoteId
     * return mixed
     */
    public function getQuoteId()
    {
        return $this->_customerSession->getQuote();
    }
}
