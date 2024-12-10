<?php
namespace Gemfind\Ringbuilder\Block\Settings\Search;

use Magento\Framework\View\Element\Template;
use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;

class Result extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * Currency Interface
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Gemfind\Ringbuilder\Model\Config\Source\Options\Resultperpage
     */
    protected $_resultperpage;

    /**
     * @param Template\Context $context
     * @param Helper $helper
     * @param ProductFactory $productFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Gemfind\Ringbuilder\Model\Config\Source\Options\Resultperpage $resultperpage
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Helper $helper,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Gemfind\Ringbuilder\Model\Config\Source\Options\Resultperpage $resultperpage,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_cookieManager = $cookieManager;
        $this->_resultperpage = $resultperpage;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->getUrl('ringbuilder/settings/ringsearch', ['_secure' => true]);
    }

     /**
      * @return string
      */
    public function getSiteUrl()
    {
        return $this->_helper->getSiteUrl();
    }

    /**
     * @return string
     */
    public function getRingViewUrl($sku, $name)
    {
        $metaltype = '14k-white-gold-metaltype-';
        $name = strtolower(str_replace(' ', '-', $name));
        $sku = '-sku-'.str_replace(' ', '-', $sku);
        return $this->getUrl('ringbuilder/settings/view', ['path' => $metaltype.$name.$sku, '_secure' => true]);
    }

    /**
     * Validate Request
     *
     * @return array
     */
    private function validateRequest()
    {
        $result = ["is_valid" => true, "response" => ""];
        $request = $this->getRequest()->getParams();
        if ($request == null) {
            $ring = [
                'meta' => ['code' => 400, 'message' => __('No arguments supplied.')],
                'data' => [],
                'pagination' => [],
                'perpage' => $this->_helper->getResultPerPageforRing()
            ];
            $result = ["is_valid" => false, "response" => $ring];
        }

        if (!is_array($request)) {
            $ring = [
                'meta' => ['code' => 400, 'message' => $request],
                'data' => [],
                'pagination' => [],
                'perpage' => $this->_helper->getResultPerPageforRing()
            ];
            $result = ["is_valid" => false, "response" => $ring];
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getRings()
    {
        $request = $this->getRequest()->getParams();
        $result = $this->validateRequest();
        if (!$result["is_valid"]) {
            return $result["response"];
        }

        if (empty($request['currentpage'])) {
            $request['currentpage'] = 1;
        }

        $shapesContent = $this->getValue('ring_shape');
        $collectionContent = $this->getValue('ring_collection');
        $metalContent = $this->getValue('ring_metal');
        $settingid = $this->getValue('settingid');
        $filtermode = $this->getValue('settingid');
        /** Get Sort Order Data */
        $sortOrder = $this->getSortOrder();
        $orderby = $sortOrder['orderby'];
        $direction = $sortOrder['direction'];
        /** Get Price Range Data */
        $priceRange = $this->getPriceRange();
        $price_from = $priceRange['price_from'];
        $price_to = $priceRange['price_to'];

        $resultPerPage = $this->_helper->getResultPerPageforRing();
        $itemPerPage = (int) $this->getRequest()->getParam("itemperpage");
        if (empty($itemPerPage)) {
            $itemPerPage = $resultPerPage;
        }

        // Create the request array to sumbit to gemfind
        $requestData = [
            'shapes' => $shapesContent,
            'ring_metal' => $metalContent,
            'ring_collection' => $collectionContent,
            'price_from' => $price_from,
            'price_to' => $price_to,
            'page_number' => $request['currentpage'],
            'page_size' => $itemPerPage,
            'sort_by' => $orderby,
            'sort_direction' => $direction,
            'settingId' => $settingid,
            'filtermode' => $filtermode,
        ];

        $requestData = $this->prepareRequestData($requestData);
        $result = $this->_helper->sendRingRequest($requestData);
        $num = ceil($result['total'] / $resultPerPage);
        if ($request['currentpage'] > $num) {
            $requestData['page_number'] = 1;
            $request['currentpage'] = 1;
            $result = $this->_helper->sendRingRequest($requestData);
        }

        $ring = [
            'meta' => ['code' => 404, 'message' => "No Product Found"],
            'data' => [],
            'pagination' => ['total' => $result['total']],
            'perpage' => $resultPerPage
        ];
        if ($result['rings'] != null || $result['total'] != 0) {
            $count = 0;
            if ($request['currentpage'] > 1) {
                $count = ($request['itemperpage']) ?
                    $request['itemperpage'] : $resultPerPage * ($request['currentpage'] - 1);
            }

            $ring = [
                'meta' => ['code' => 200],
                'data' => $result['rings'],
                'pagination' => [
                    'currentpage' => $request['currentpage'],
                    'count'     => $count,
                    'limit'     => count($result['rings']),
                    'total'     => $result['total']
                ],
                'perpage' => $itemPerPage
            ];
        }

        return $ring;
    }

    /**
     * Get Value
     *
     * @param string $field
     * @return string
     */
    private function getValue($field)
    {
        $value = "";
        $request = $this->getRequest()->getParams();
        if (array_key_exists($field, $request)) {
            $value = $request[$field];
        }

        return $value;
    }

    /**
     * Prepare Request Data
     *
     * @param array $requestData
     * @return array
     */
    private function prepareRequestData($requestData)
    {
        $request = $this->getRequest()->getParams();
        if (array_key_exists('caratvalue', $request)) {
            $requestData['caratvalue'] = $request['caratvalue'];
        }

        if (array_key_exists('caratminvalue', $request)) {
            $requestData['caratminvalue'] = $request['caratminvalue'];
        }

        if (array_key_exists('caratmaxvalue', $request)) {
            $requestData['caratmaxvalue'] = $request['caratmaxvalue'];
        }

        return $requestData;
    }

    /**
     * Get Price Range
     *
     * @return array
     */
    private function getPriceRange()
    {
        $request = $this->getRequest()->getParams();
        $price_from = '';
        $price_to = '';
        if (isset($request['price'])) {
            $price_from = (intval($request["price"]["from"])) ? intval(str_replace(',', '', $request["price"]["from"])) : '';
            $price_to = (intval($request["price"]["to"])) ? intval(str_replace(',', '', $request["price"]["to"])) : '';
        }

        return [
            "price_from" => $price_from,
            "price_to" => $price_to
        ];
    }

    /**
     * Get Sort Order
     *
     * @return array
     */
    private function getSortOrder()
    {
        $request = $this->getRequest()->getParams();
        $orderby = 'cost';
        $direction = 'asc';

        if (isset($request['orderby']) && $request['orderby'] == "cost-h-l") {
            $orderby = 'cost';
            $direction = 'desc';
        }
        return [
            "orderby" => $orderby,
            "direction" => $direction
        ];
    }

    /**
     * @return mixed
     */
    public function getUrlString()
    {
        return $this->getRequest()->getUriString();
    }

    /**
     * @return array
     */
    public function getResultsPerPageOptions()
    {
        return $this->_resultperpage->getAllOptions();
    }

    /**
     * @return Mode
     */
    public function getFiltermode()
    {
        $request = $this->getRequest()->getParams();
        return ($request['filtermode']) ? $request['filtermode'] : '';
    }

    public function getGemfindEnabledPoweredBy()
    {
        return $this->_helper->isGemfindEnabledPoweredBy();
    }
}
