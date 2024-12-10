<?php declare(strict_types=1);

namespace DiamondIcq\OpenGraph\Block;

use DiamondIcq\OpenGraph\Model\AlternateLink;
use DiamondIcq\OpenGraph\Service\LocaleFormatterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Header block meta
 */
class General extends Template
{
    /**
     * @var UrlInterface
     */
    protected UrlInterface $url;

    /**
     * @var ResolverInterface
     */
    protected ResolverInterface $localeResolver;

    /**
     * @var LocaleFormatterInterface
     */
    protected LocaleFormatterInterface $localeFormatter;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * General constructor.
     *
     * @param Context $context
     * @param UrlInterface $url
     * @param ResolverInterface $localeResolver
     * @param LocaleFormatterInterface $localeFormatter
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $url,
        ResolverInterface $localeResolver,
        LocaleFormatterInterface $localeFormatter,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
        $this->localeResolver = $localeResolver;
        $this->localeFormatter = $localeFormatter;
    }

    /**
     * Generate alternate links
     *
     * @return AlternateLink[]
     */
    public function getAlternateLinks(): array
    {
        $currentUrl = $this->url->getCurrentUrl();
        if (!str_ends_with($currentUrl, '/')) {
            $currentUrl .= '/';
        }
        if (strpos($currentUrl, '/hk/') === false
            && strpos($currentUrl, '/en/') === false
        ) {
            $currentUrl = $this->url->getCurrentUrl() . 'en/';
        }

        $storeLocaleMap = $this->getStoreLocaleMap();
        return [
            new AlternateLink(
                str_replace('/hk/', '/en/', $currentUrl),
                (string) $storeLocaleMap['en']
            ),
            new AlternateLink(
                str_replace('/en/', '/hk/', $currentUrl),
                (string) $storeLocaleMap['hk']
            ),
        ];
    }

    /**
     * Example: ['en' => 'en_US', 'hk' => 'zh_Hant_TW', 'cn' => 'en_US']
     *
     * @return array
     */
    protected function getStoreLocaleMap(): array
    {
        $storeLocaleMap = [];
        $configPath = 'general/locale/code';

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $locale = $this->scopeConfig->getValue(
                $configPath,
                ScopeInterface::SCOPE_STORE,
                $store->getId()
            );
            $storeLocaleMap[$store->getCode()] =
                $this->localeFormatter->format($locale);
        }
        return $storeLocaleMap;
    }
}
