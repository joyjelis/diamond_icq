<?php

namespace Customer\Sell\Block\Quote;

use Customer\Sell\Helper\Data;
use Customer\Sell\Model\ResourceModel\Sell\CollectionFactory;
use Customer\Sell\Model\SellFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class Items extends Template
{

    const SORT_ASC = 'ASC';

    const SORT_DESC = 'DESC';

    const PAGE_LIMIT = [5, 10, 15, 20, 30];

    protected $_template = "Customer_Sell::quote/list/items.phtml";

    protected $_page = 1;

    protected $_limit = 5;

    protected $_filters = [
        'loose_diamond' => 'Loose Diamond',
        'diamond_ring' => 'Diamond Ring',
        'diamond_earring' => 'Diamond Earring',
        'diamond_pendant' => 'Diamond Pendant',
        'diamond_bracelet' => 'Diamond Bracelet',
        'diamond_bangle' => 'Diamond Bangle',
        'others' => 'Others',
    ];

    protected $_sortfield = [
        'date_desc' => 'created_at',
        'date_asc' => 'created_at',
        'quote_desc' => 'quote',
        'quote_asc' => 'quote',
        'price_desc' => 'price',
        'price_asc' => 'price',
        'jewellery_desc' => 'jewellery_type',
        'jewellery_asc' => 'jewellery_type',
    ];

    protected $_sortby = [
        'date_desc' => self::SORT_DESC,
        'date_asc' => self::SORT_ASC,
        'quote_desc' => self::SORT_DESC,
        'quote_asc' => self::SORT_ASC,
        'price_desc' => self::SORT_DESC,
        'price_asc' => self::SORT_ASC,
        'jewellery_desc' => self::SORT_DESC,
        'jewellery_asc' => self::SORT_ASC,
    ];

    protected $_templateOptions = [
        'gridview' => "Customer_Sell::quote/grid/items.phtml",
        'listview' => "Customer_Sell::quote/list/items.phtml",
    ];

    /**
     * @var CollectionFactory
     */
    protected $sellCollectionFactory;

    /**
     * @var SessionFactory
     */
    private $_customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * Index constructor.
     * @param Context $context
     * @param SessionFactory $customerSession
     * @param CollectionFactory $sellCollectionFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionFactory $customerSession,
        SellFactory $sellFactory,
        CollectionFactory $sellCollectionFactory,
        Data $helper,
        StoreManagerInterface $storeManagerInterface,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_customerSession = $customerSession;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->sellFactory = $sellFactory;
        $this->sellCollectionFactory = $sellCollectionFactory;
        parent::__construct($context, $data);
        $this->setFilterTemplate();
    }

    public function getItemStatus($item)
    {
        $status = $item->getStatus();
        $allstatus = $this->sellFactory->create()->getStatusOptions();
        return isset($allstatus[$status]) ? $allstatus[$status] : "";
    }

    public function setFilterTemplate()
    {
        $template = $this->getRequest()->getParam('currentview');
        if ($template) {
            if (array_key_exists($template, $this->_templateOptions)) {
                $this->_template = $this->_templateOptions[$template];
            }
        }
    }

    public function setPage()
    {
        $page = $this->getRequest()->getParam('p');
        if ($page) {
            $this->_page = $page;
        }
    }

    public function setPageLimit()
    {
        $limit = $this->getRequest()->getParam('limit');
        if ($limit) {
            $this->_limit = $limit;
        }
    }

    /**
     * Set Sell Page Title
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Quotes | DiamondICQ'));
        $page_size = array_combine(self::PAGE_LIMIT, self::PAGE_LIMIT);
        $collection = $this->getCollection();
        if ($collection) {
            $blockName = 'customer.sell.pager_' . rand();
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                $blockName
            )->setAvailableLimit($page_size)->setShowPerPage(true)->setCollection($collection);
            $this->setChild('pager', $pager);
            $collection->load();
        }

        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function applyFilters($collection)
    {
        $filter = $this->getRequest()->getParam('sortby');
        if ($filter) {
            if (array_key_exists($filter, $this->_filters)) {
                $collection->addFieldToFilter(
                    'jewellery_type',
                    ['like' => '%' . $this->_filters[$filter] . '%']
                );
                return $collection;
            }
        }

        return $collection;
    }

    public function applySorting($collection)
    {
        $sortby = $this->getRequest()->getParam('sortby');
        $field = 'created_at';

        if ($sortby) {
            $field = $this->_sortfield[$sortby];
            if (array_key_exists($sortby, $this->_sortby)) {
                $collection->setOrder($field, $this->_sortby[$sortby]);
                return $collection;
            }
        } else {
            $collection->setOrder($field, self::SORT_DESC);
            return $collection;
        }

        return $collection;
    }

    /**
     * @return \Customer\Sell\Model\ResourceModel\Sell\Collection|false
     */
    public function getCollection()
    {
        if (!$this->_customerSession->create()->getId()) {
            return false;
        }

        $customerId = $this->_customerSession->create()->getCustomer()->getId();
        if ($customerId) {

            $this->setPage();
            $this->setPageLimit();

            $collection = $this->sellFactory->create()
                ->getCollection()
                ->setPageSize($this->_limit)
                ->setCurPage($this->_page)
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('quote', ['nlike' => '%TP#%']);

            $collection = $this->applyFilters($collection);
            $collection = $this->applySorting($collection);
            return $collection;
        }
    }

    /**
     * Get First Image for Sell record
     *
     * @param string $image
     * @return string
     */
    public function getFirstImage($image)
    {
        $mediaUrl = $this->storeManagerInterface->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . 'sell/dimages/' . $image;
    }

    public function getImage($item)
    {
        $image = $item->getImage();
        if ($image) {
            $image = explode(',', $image);
            return $this->generateImageUrl($image[0], $item->getData('sell_id'));
        } else {
            return $this->generateImageUrl(rand(), $item->getData('sell_id'));
        }
    }

    public function generateImageUrl($img, $sellId)
    {
        return $this->getUrl('*/*/loadimage', ['_secure' => true, 'load' => base64_encode(json_encode([$img, $sellId]))]);
    }

    /**
     * Get View Page URL
     *
     * @param object $item
     */
    public function getViewUrl($item)
    {
        $param = ['id' => $item->getData('sell_id')];
        $url = $this->getUrl('*/*/view', $param);
        return $url;
    }

    public function IsNotQulify($item)
    {
        return $item->getStatus() == \Customer\Sell\Model\Sell::NOT_QUALIFY_STATUS;
    }

    public function getPrice($item)
    {
        $price = $item->getPrice();
        $offerPrice = $item->getOfferPrice();

        if ($price && empty($offerPrice)) {
            return $this->helper->getCurrencyWithFormat($price);
        }

        if ($offerPrice) {
            return $this->helper->getCurrencyWithFormat($offerPrice);
        }

        return false;
    }
}
