<?php
namespace Magneto\CurrencyChangeTab\Block;
 
/**
 * Customform content block
 */
class Customform extends \Magento\Framework\View\Element\Template
{
    private $currencySource;
    private $httpContext;
    private $helperClass;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magneto\CurrencyChangeTab\Model\Source\CurrencyOption $currencySource
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magneto\CurrencyChangeTab\Helper\Data $helperClass
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magneto\CurrencyChangeTab\Model\Source\CurrencyOption $currencySource,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magneto\CurrencyChangeTab\Helper\Data $helperClass,
        array $data = []
    ) {
        $this->currencySource = $currencySource;
        $this->httpContext = $httpContext;
        $this->helperClass = $helperClass;
        parent::__construct($context, $data);
    }

    public function getAllCurrencies()
    {
        $options = $this->currencySource->getAllOptions();
        $result = [];
        foreach ($options as $option) {
            $result[$option['value']] = $option["label"];
        }

        return $result;
    }

    public function getCustomerCurrentCurrency()
    {
        return $this->helperClass->getPreferredCurrency();
    }
}
