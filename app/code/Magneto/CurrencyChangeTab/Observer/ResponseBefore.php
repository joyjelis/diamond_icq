<?php
namespace Magneto\CurrencyChangeTab\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magneto\CurrencyChangeTab\Helper\Data;

class ResponseBefore implements ObserverInterface
{
    private $dataHelper;

    /**
     * @param Data $dataHelper
     */
    public function __construct(
        Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /* @phpstan-ignore-next-line */
    public function execute(Observer $observer)
    {
        if (!empty($this->dataHelper->isFirstTime())) { 
            return;
        }

        $this->dataHelper->updateIsFirstTime();
        $currency = $this->dataHelper->getFinalCurrency();
        $currentCurrency = $this->dataHelper->getCurrentCurrency();
        if ($currentCurrency != $currency) {
            $url = $this->dataHelper->getSwitchUrl($currency);
            $observer->getResponse()->setRedirect($url);
        }

        /* @phpstan-ignore-next-line */
        return $this;
    }
}
