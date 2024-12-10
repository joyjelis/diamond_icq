<?php
namespace Magneto\IncludePriceReceipt\Helper;

use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
class Data extends AbstractHelper {

    const INCLUDE_PRICE_ENABLED = 'includepricereceipt_action/general/enabled';
    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled($storeId = NULL){
        return $this->scopeConfig->getValue(self::INCLUDE_PRICE_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }
     public function getTemplate() {
        $template = $this->isEnabled() ? 'Magneto_IncludePriceReceipt::order/view/customOrderEmail.phtml' : 'Magento_Sales::email/items.phtml';
        return $template;
    }
}
