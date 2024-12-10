<?php

namespace Magneto\RingBuilder\Block\Override\Diamond;

use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Config;
use Magento\Store\Model\StoreManagerInterface;

class Index extends \Gemfind\Ringbuilder\Block\Diamond\Index
{
    protected $helper;
    protected $sessionManager;
    protected $storeManager;
    protected $localeCurrency;
    protected $cookieManager;
    protected $cookieMetadataFactory;
    protected $pageConfig;

    public function __construct(
        Context $context,
        Helper $helper,
        SessionManagerInterface $sessionManager,
        StoreManagerInterface $storeManager,
        CurrencyInterface $localeCurrency,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        Config $pageConfig,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $helper,
            $sessionManager,
            $storeManager,
            $localeCurrency,
            $cookieManager,
            $cookieMetadataFactory,
            $data
        );
        $this->pageConfig = $pageConfig;
        $this->pageConfig->getTitle()->set($this->getMetaTitle());
        $this->pageConfig->setKeywords($this->getMetaKeywords());
        $this->pageConfig->setDescription($this->getDescription());
    }

    private function getMetaTitle()
    {
        return __('Design Your Own Ring Online at Diamond ICQ Hong Kong');
    }

    private function getMetaKeywords()
    {
        return __('Buy Diamonds, Shopping for Diamonds, Best place to by Diamonds');
    }

    private function getDescription()
    {
        // @codingStandardsIgnoreStart
        return __('Design your own unique ring. Build your dream ring with our easy-to-use tools. Explore our range of customisation options. Shop your diamond ring today.');
        // @codingStandardsIgnoreEnd
    }
}
