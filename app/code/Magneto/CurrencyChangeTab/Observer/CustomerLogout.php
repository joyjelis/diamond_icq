<?php
namespace Magneto\CurrencyChangeTab\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magneto\CurrencyChangeTab\Helper\Data;

class CustomerLogout implements ObserverInterface
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

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->dataHelper->processCurrency();
        // updating flag after logout
        $this->dataHelper->updateIsFirstTime(0);
    }
}
