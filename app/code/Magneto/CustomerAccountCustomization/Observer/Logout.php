<?php
namespace Magneto\CustomerAccountCustomization\Observer;

use Amasty\Base\Model\Serializer;
use Amasty\Geoip\Model\Geolocation;
use Amasty\GeoipRedirect\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class Logout implements \Magento\Framework\Event\ObserverInterface
{
    const LOCAL_IP = '127.0.0.1';
    const URL_TRIM_CHARACTER = '/';
    const HOME = 'cms_index_index';
    const FIRST_REDIRECT_WITH_POPUP = null;

    protected $redirectAllowed = false;

    protected $addressPath = [
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR'
    ];
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Geolocation
     */
    private $geolocation;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;
    /**
     * @var ResolverInterface
     */
    private $resolver;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var Data
     */
    private $geoipHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param RemoteAddress $remoteAddress
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $geoipHelper
     * @param Geolocation $geolocation
     * @param Session $customerSession
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param SessionManagerInterface $sessionManager
     * @param Serializer $serializer
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        RemoteAddress $remoteAddress,
        ScopeConfigInterface $scopeConfig,
        Data $geoipHelper,
        Geolocation $geolocation,
        Session $customerSession,
        StoreCookieManagerInterface $storeCookieManager,
        SessionManagerInterface $sessionManager,
        Serializer $serializer,
        LoggerInterface $logger,
        RequestInterface $request
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->geoipHelper = $geoipHelper;
        $this->storeManager = $storeManager;
        $this->geolocation = $geolocation;
        $this->customerSession = $customerSession;
        $this->serializer = $serializer;
        $this->sessionManager = $sessionManager;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return string|void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $currentIp = $this->getCurrentIp($this->request);
        $this->logger->info('Before logout get current IP:'.$currentIp);
        $location = $this->geolocation->locate($currentIp);
        $country = $location->getCountry();
        $this->logger->info('Before logout get country:'.$country ?? '');
        return $this->_setNewCurrency($country ?? 'US');
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getCurrentIp(RequestInterface $request)
    {
        foreach ($this->addressPath as $path) {
            $ip = $request->getServer($path);

            if ($ip) {
                if (strpos($ip, ',') !== false) {
                    $addresses = explode(',', $ip);
                    foreach ($addresses as $address) {
                        if (trim($address) != self::LOCAL_IP) {
                            return trim($address);
                        }
                    }
                } else {
                    if ($ip != self::LOCAL_IP) {
                        return $ip;
                    }
                }
            }
        }

        return $this->remoteAddress->getRemoteAddress();
    }


    /**
     * @param $country
     * @param $scopeStores
     * @param $currentStoreId
     * @return $this
     */
    protected function _setNewCurrency($country = 'US')
    {
        $currencyMapping = $this->serializer->unserialize(
            $this->scopeConfig->getValue(
                'amgeoipredirect/country_currency/currency_mapping'
            )
        );
        $this->logger->info('Currency mapping:'.print_r($currencyMapping,1));
        foreach ($currencyMapping as $countries => $currency) {
            if (strpos($countries, $country) !== false
                && $this->storeManager->getStore()
                    ->getCurrentCurrencyCode() != $currency
            ) {
                $this->logger->info('setting  currency for country :'.$country);
                $this->logger->info('setting new currency found from :'.print_r($countries,1));
                $this->storeManager->getStore()->setCurrentCurrencyCode($currency);
            }
        }
        return $this;
    }
}
