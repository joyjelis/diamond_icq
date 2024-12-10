<?php
namespace Magneto\CurrencyChangeTab\Plugin;
use Magento\Directory\Controller\Currency\SwitchAction;
use Magneto\CurrencyChangeTab\Helper\Data as DataHelper;

class AfterCurrencySwitch
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }
    
    /* @phpstan-ignore-next-line */
    public function afterExecute(
        SwitchAction $subject,
        $result
    ) {
        $currency = (string) $subject->getRequest()->getParam('currency');
        $this->dataHelper->setOptedCurrency($currency);
        return $result;
    }
}
