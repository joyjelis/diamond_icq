<?php

namespace Magneto\ProductDeliveryDate\Helper;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_IS_ENABLE = "productestimateddeliverydate_action/general/enabled";
    protected $checkoutSession;
    protected $session;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Catalog\Model\Product $productModel,
        CheckoutSession $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->timezoneInterface = $timezoneInterface;
        $this->productModel = $productModel;
        parent::__construct($context);
    }

    public function isEnabled()
    {
        return $this->getConfig(self::XML_IS_ENABLE);
    }

    public function getSetting()
    {
        $setting = [
            'is_enable' => $this->getConfig(self::XML_IS_ENABLE),
        ];

        return $setting;
    }

    /**
     * Get Config values
     *
     * @param string $config
     *
     * return string
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMaxDayQuoteProducts()
    {
        $expectedDeliveryDate = '';
        return $expectedDeliveryDate;
        
        if ($this->isEnabled()) {
            $currentQuoteId = (int)$this->checkoutSession->getQuote()->getId();
            $quote = $this->checkoutSession->getQuote();
            $items = $quote->getAllItems();
            $deliveryDaysArray = [];
//            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            foreach ($items as $quote_item) {
                $productDeliveryDays = '';
                $itemsId = $quote_item->getId();
                $productId = $quote_item->getProductId();
                //$_product = $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
                $_product = $this->productModel->load($productId);
                $productDeliveryDays = $_product->getData('product_delivery_days');
                if (trim($productDeliveryDays)!='') {
                    $deliveryDaysArray[] = trim($productDeliveryDays);
                }
            }
            if (!empty($deliveryDaysArray)) {
                rsort($deliveryDaysArray);
                $deliveryDaysArrayFirstIndex = $deliveryDaysArray[0];
                $currentDate =  date('Y-m-d');
                $expectedDeliveryDate = date("Y-m-d", strtotime($currentDate." + $deliveryDaysArrayFirstIndex day"));
                $expectedDeliveryDate = $this->showDeliveryDateFormat($expectedDeliveryDate);
            }

        }
        return $expectedDeliveryDate;
    }

    public function showDeliveryDateFormat($currentDate)
    {
        //$finalDateFormat = 'l, d F Y';
        $finalDateFormat = 'D, d M Y';
        //$expectedDeliveryDate = date($finalDateFormat, strtotime($currentDate));
        /*$expectedDeliveryDate = $this->timezoneInterface->formatDate(
             $currentDate,
             \IntlDateFormatter::FULL,
             false
         );
        */
        $expectedDeliveryDate = $this->timezoneInterface->date(new \DateTime($currentDate))->format($finalDateFormat);
        return $expectedDeliveryDate;
    }
}
