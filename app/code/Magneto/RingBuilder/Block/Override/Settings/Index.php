<?php

namespace Magneto\RingBuilder\Block\Override\Settings;

use Magento\Framework\View\Element\Template\Context;
use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Page\Config;

class Index extends \Gemfind\Ringbuilder\Block\Settings\Index
{
    protected $helper;
    protected $sessionManager;
    protected $_storeManager;
    protected $_cookieManager;
    protected $_cookieMetadataFactory;
    protected $pageConfig;

    public function __construct(
        Context $context,
        Helper $helper,
        SessionManagerInterface $sessionManager,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        array $data = [],
        Config $pageConfig
    ) {
        parent::__construct($context, $helper, $sessionManager, $storeManager, $cookieManager, $cookieMetadataFactory, $data);
        $this->pageConfig = $pageConfig;
        $this->pageConfig->getTitle()->set($this->getMetaTitle());
        $this->pageConfig->setDescription($this->getDescription());
    }

    private function getMetaTitle()
    {
        return __('Design Your Own Ring Online at Diamond ICQ Hong Kong');
    }

    private function getDescription()
    {
        // @codingStandardsIgnoreStart
        return __('Design your own unique ring. Build your dream ring with our easy-to-use tools. Explore our range of customisation options. Shop your diamond ring today.');
        // @codingStandardsIgnoreEnd
    }
}
