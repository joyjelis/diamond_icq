<?php
namespace Magneto\CurrencyChangeTab\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magneto\CurrencyChangeTab\Helper\Data;

class CustomerLogin implements ObserverInterface
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
        // updating flag after login
        $this->dataHelper->updateIsFirstTime(0);
        $this->dataHelper->processCurrency();
    }
}
