<?php
namespace Magneto\CurrencyChangeTab\Helper;

use Amasty\Base\Model\Serializer;
use Amasty\Geoip\Model\Geolocation;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\TestFramework\Inspection\Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Framework\UrlInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Data extends AbstractHelper
{
    const LOCAL_IP = '127.0.0.1';
    const URL_TRIM_CHARACTER = '/';
    const HOME = 'cms_index_index';

    protected $redirectAllowed = false;

    protected $addressPath = [
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR'
    ];
    /**
     * @var LoggerInterface
     */
    private $logger;
    private $currentCustomerSession;
    private $customerRepository;
    private $_storeManager;
    private $remoteAddress;
    private $geolocation;
    private $serializer;
    private $request;
    private $cacheTypeList;
    private $cacheFrontendPool;
    private $curl;
    private $coreSession;
    private $url;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $currentCustomerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param RemoteAddress $remoteAddress
     * @param ScopeConfigInterface $scopeConfig
     * @param Geolocation $geolocation
     * @param Serializer $serializer
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param Curl $curl
     * @param CoreSession $coreSession
     * @param UrlInterface $url
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $currentCustomerSession,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        RemoteAddress $remoteAddress,
        ScopeConfigInterface $scopeConfig,
        Geolocation $geolocation,
        Serializer $serializer,
        LoggerInterface $logger,
        RequestInterface $request,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        Curl $curl,
        CoreSession $coreSession,
        UrlInterface $url
    ) {
        $this->currentCustomerSession = $currentCustomerSession;
        $this->customerRepository = $customerRepository;
        $this->_storeManager = $storeManager;
        $this->logger = $logger;
        $this->remoteAddress = $remoteAddress;
        $this->geolocation = $geolocation;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->curl = $curl;
        $this->coreSession = $coreSession;
        $this->url = $url;
        parent::__construct($context);
    }

    public function getPreferredCurrency()
    {
        $currentCurrency = '';
        try {
            if ($this->currentCustomerSession->isLoggedIn()) {
                $customerId = $this->currentCustomerSession->getCustomer()->getId();
                $customer = $this->customerRepository->getById($customerId);
                $customerData = $customer->__toArray();
                if (isset($customerData['custom_attributes']['preferred_currency'])
                    && trim($customerData['custom_attributes']['preferred_currency']['value']) != '') {
                    $currentCurrency = trim($customerData['custom_attributes']['preferred_currency']['value']);
                }
            }
        } catch (Exception $e) {
            $currentCurrency = '';
        }

        return $currentCurrency;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getCurrentCustomerCurrency()
    {
        $currentCurrency = '';
        if ($this->currentCustomerSession->isLoggedIn()) {
            $customerId = $this->currentCustomerSession->getCustomer()->getId();
            $customer = $this->customerRepository->getById($customerId);
            $customerData = $customer->__toArray();
            if (isset($customerData['custom_attributes']['preferred_currency'])
                && trim($customerData['custom_attributes']['preferred_currency']['value'])!='') {
                $currentCurrency = trim($customerData['custom_attributes']['preferred_currency']['value']);

                $this->logger->info('Logged in customer - preferred currency:'.$currentCurrency);
            } else {
                //set currency based on location
                $this->logger->info('NOT Logged customer NO preferred currency');
                try {
                    $currentIp = $this->getCurrentIp($this->request);
                    $this->logger->info('NOT Logged customer start process');
                    $this->logger->info('NOT Logged customer get current IP:'.$currentIp);
                    $location = $this->geolocation->locate($currentIp);
                    $country = $location->getCountry();
                    $this->logger->info('NOT Logged customer get current country:'.$country);
                    if (!$country) {
                        $this->logger->info('NOT Logged customer - country NOT identified');
                        $currentCurrency = $this->getSiteCurrentCurrencyCode();
                        $this->logger->info('NOT Logged customer - setting site currency code:'.$currentCurrency);
                    } else {
                        $this->logger->info('NOT Logged customer - country identified');
                        /* @phpstan-ignore-next-line */
                        $currentCurrency =  $this->_setNewCurrency($country ?? 'HK');
                        $this->logger->info('NOT Logged customer - setting site currency code:'.$currentCurrency);
                    }
                } catch (Exception $error) {
                    $this->logger->info('NOT Logged customer - Exception error trapped:'.$error->getMessage());
                    $currentCurrency = $this->getSiteCurrentCurrencyCode();
                    $this->logger->info('NOT Logged customer - Exceptions setting site currency code:'.$currentCurrency);
                }
            }
        }

        return $currentCurrency;
    }

    public function updateCurrentCurrency($currencyCode)
    {
        $this->_storeManager->getStore()->setCurrentCurrencyCode($currencyCode);
    }

    public function getSiteCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @param RequestInterface $request
     * @return string
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function getCurrentIp(RequestInterface $request)
    {
        foreach ($this->addressPath as $path) {
            /* @phpstan-ignore-next-line */
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
     * @return string
     */
    protected function _setNewCurrency($country = 'HK')
    {
        $currencyMapping = $this->serializer->unserialize(
            $this->scopeConfig->getValue(
                'amgeoipredirect/country_currency/currency_mapping'
            )
        );
        foreach ($currencyMapping as $countries => $currency) {
            if (strpos($countries, $country) !== false
                && $this->_storeManager->getStore()
                    ->getCurrentCurrencyCode() != $currency
            ) {
                return $currency;
            }
        }
        return 'HKD';
    }

    /**
     * @param bool $refreshCache
     * @return void
     *  @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function processCurrency($refreshCache = true)
    {
        $currency = $this->getFinalCurrency();
        $this->setCurrency($currency, $refreshCache);
    }

    /**
     * Refresh Cache
     *
     * @return void
     */
    public function refrehCache()
    {
        $types = ['block_html', 'reflection', 'db_ddl', 'full_page',];
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }

        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

    /**
     * Get Country Code by Geolocation
     *
     * @return string
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getCountryCode()
    {
        $countryCode = "";
        try {
            $ip = $this->remoteAddress->getRemoteAddress();
            $url = "http://www.geoplugin.net/json.gp";
            if ($this->isValidIp($ip)) {
                $url .= "?ip=".$ip;
            }

            $this->curl->get($url);
            $response = $this->curl->getBody();
            $ipData = json_decode($response);
            if (is_object($ipData) && property_exists($ipData, "geoplugin_countryCode")) {
                $countryCode = $ipData->geoplugin_countryCode;
            } else {
                $currentIp = $this->getCurrentIp($this->request);
                $location = $this->geolocation->locate($currentIp);
                $countryCode = $location->getCountry();
            }
        } catch (Exception $e) {
            $countryCode = "";
        }

        return $countryCode;
    }

    /**
     * Check Is Valid IP
     *
     * @param string $ip
     * @return bool
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function isValidIp($ip)
    {
        /* @phpstan-ignore-next-line */
        if ($ip == "::1") { // checking localhost
            return false;
        } elseif (filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set Currency
     *
     * @param string $currency
     * @param bool $refreshCache
     * @return void
     *  @SuppressWarnings(PMD.BooleanArgumentFlag)
     */
    public function setCurrency($currency, $refreshCache = true)
    {
        $currentCurrency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        if ($currency != $currentCurrency && $this->isAvailable($currency)) {
            $this->_storeManager->getStore()->setCurrentCurrencyCode($currency);
            if ($refreshCache) {
                $this->refrehCache();
            }
        }
    }

    public function getCurrentCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Check If Currency is Available
     *
     * @param string $currency
     * @return bool
     */
    public function isAvailable($currency)
    {
        $availableCurrencies = $this->_storeManager->getStore()->getAvailableCurrencyCodes();
        foreach ($availableCurrencies as $currencyCode) {
            if ($currencyCode == $currency) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        $countryCode = $this->getCountryCode();
        $currency = "USD";
        if ($countryCode == "HK") {
            $currency = "HKD";
        }

        return $currency;
    }

    /**
     * Get Final Currency
     *
     * @return string
     */
    public function getFinalCurrency()
    {
        $currency = $this->getOptedCurrency();
        if (empty($currency)) {
            if ($this->currentCustomerSession->isLoggedIn()) {
                $currency = $this->getPreferredCurrency();
            }

            if (empty($currency)) {
                $currency = $this->getCurrency();
            }
        }

        return $currency;
    }

    /**
     * Get Customer Id
     *
     * @return int
     */
    public function getCustomerId()
    {
        $customerId = 0;
        if ($this->currentCustomerSession->isLoggedIn()) {
            $customerId = $this->currentCustomerSession->getCustomer()->getId();
        }

        return $customerId;
    }

    /**
     * Set Opted Currency
     *
     * @param string $currency
     * @return void
     */
    public function setOptedCurrency($currency)
    {
        if (!empty($currency)) {
            $customerId = $this->getCustomerId();
            $opted = $this->coreSession->getIcqOptedCurrency();
            $opted[$customerId] = $currency;
            $this->coreSession->setIcqOptedCurrency($opted);
        }
    }

    /**
     * Get Opted Currency
     *
     * @return string
     */
    public function getOptedCurrency()
    {
        $customerId = $this->getCustomerId();
        $opted = $this->coreSession->getIcqOptedCurrency();
        if (empty($opted[$customerId])) {
            return "";
        }

        return $opted[$customerId];
    }

    /**
     * Check If Opted Currency
     *
     * @return bool
     */
    public function isOptedCurrency()
    {
        $opted = $this->coreSession->getIcqOptedCurrency();
        if (empty($opted)) {
            return false;
        }

        $customerId = $this->getCustomerId();
        if (!empty($opted[$customerId])) {
            return true;
        }

        return false;
    }

    public function isFirstTime()
    {
        return $this->coreSession->getIcqIsFirstTime();
    }

    public function updateIsFirstTime($value = 1)
    {
        return $this->coreSession->setIcqIsFirstTime($value);
    }
    
    public function getSwitchUrl($currency)
    {
        return $this->url->getUrl("directory/currency/switch", ["currency" => $currency]);
    }
}
