<?php
namespace Magneto\CurrencyChangeTab\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\FrontControllerInterface;
use Magneto\CurrencyChangeTab\Helper\Data as DataHelper;

class Action
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

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        FrontControllerInterface $subject,
        \Closure $proceed,
        RequestInterface $request
    ) {
        if (!empty($this->dataHelper->isFirstTime())) { 
            return $proceed($request);
        }

        $this->dataHelper->updateIsFirstTime();
        $this->dataHelper->processCurrency(false);
        return $proceed($request);
    }
}
