<?php
namespace Travash\Education\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Result\Page as ResultPage;

/**
 * Class Data
 * @package Travash\Education\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     *
     */
    const CAT_MEDIA_PATH = '';
    /**
     *
     */
    const XML_PATH_ENABLED = 'education/general/enable';
    /**
     *
     */
    const XML_PATH_HEADER_LINK = 'education/general/header_link';
    /**
     *
     */
    const XML_PATH_FOOTER_LINK = 'education/general/footer_link';
    /**
     *
     */
    const XML_PATH_PAGE_URL = 'education/general/page_url';
    /**
     *
     */
    const XML_PATH_LINK_TITLE = 'education/general/link_title';
    /**
     *
     */
    const XML_PATH_PAGE_TITLE = 'education/general/page_title';
    /**
     *
     */
    const XML_PATH_PAGE_DESCRIPTION = 'education/general/page_description';
    /**
     *
     */
    const XML_PATH_META_TITLE = 'education/general/meta_title';
    /**
     *
     */
    const XML_PATH_META_KEYWORDS = 'education/general/meta_keywords';
    /**
     *
     */
    const XML_PATH_META_DESCRIPTION = 'education/general/meta_description';
    /**
     *
     */
    const XML_PATH_SCHEMA_SCRIPT = 'education/general/schema_script';
    /**
     *
     */
    const XML_PATH_PAGE_LAYOUT = 'education/general/page_layout';
    /**
     *
     */
    const XML_PATH_CATEGORY_URL_PREFIX = 'education/general/category_url_prefix';
    /**
     *
     */
    const XML_PATH_CATEGORY_URL_SUFFIX = 'education/general/category_url_suffix';
    /**
     *
     */
    const XML_PATH_VIEW = 'education/general/view';
    /**
     *
     */
    const XML_PATH_DISPLAY_MODE = 'education/general/display_mode';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\App\Config\scopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $_filesystem
     * @param \Magento\Framework\App\Config\scopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $_filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $_filesystem;
        $this->mediaDirectory = $_filesystem
            ->getDirectoryWrite(
                DirectoryList::MEDIA
            );
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $storePath
     * @return mixed
     */
    public function getStoreConfig($storePath)
    {
        $storeConfig = $this->scopeConfig->getValue(
            $storePath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeConfig;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isHeaderLinkEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HEADER_LINK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isFooterLinkEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FOOTER_LINK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_VIEW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getEducationList()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAGE_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_META_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMetaKeyword()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_META_KEYWORDS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_META_DESCRIPTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getSchemaScript()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SCHEMA_SCRIPT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getCategoryUrlPrifix()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_URL_PREFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getCategoryUrlSuffix()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_URL_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPageTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAGE_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPageDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAGE_DESCRIPTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPageUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAGE_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getLinkTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LINK_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getBaseDir()
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            self::CAT_MEDIA_PATH
        );
        return $path;
    }

    /**
     * @return string
     */
    public function getBaseUrlMedia()
    {
        /* @phpstan-ignore-next-line */
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . self::CAT_MEDIA_PATH;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        /* @phpstan-ignore-next-line */
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return mixed
     */
    public function getDisplayMode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param ResultPage $resultPage
     * @param $layoutId
     * @return $this
     */
    public function initProductLayout(
        ResultPage
        $resultPage,
        $layoutId
    ) {
        $postListLayout = $this->getEducationList();
        $pageConfig = $resultPage->getConfig();
        $pageConfig->setPageLayout($postListLayout);
        return $this;
    }

    /**
     * @param ResultPage $resultPage
     * @param $controller
     * @param $pageNo
     * @return $this
     */
    public function prepareAndRender(
        ResultPage
        $resultPage,
        $controller,
        $pageNo
    ) {
        $this->initProductLayout($resultPage, 'page_layout');
        // $currentPage = abs(intval($pageNo));
        // if ($currentPage < 1) {
        //     $currentPage = 1;
        // }
        $resultPage->getLayout();
        $listBlock = $resultPage
            ->getLayout()
            ->getBlock(
                'educationcollapse'
            );
       // $listBlock->setCurrentPage($currentPage);
        return $this;
    }

    /**
     * @param ResultPage $resultPage
     * @param $controller
     * @param $pageNo
     * @return $this
     */
    public function prepareAndRenderCat(
        ResultPage
        $resultPage,
        $controller,
        $pageNo
    ) {
        $this->initProductLayout($resultPage, 'page_layout');
        // $currentPage = abs(intval($pageNo));
        // if ($currentPage < 1) {
        //     $currentPage = 1;
        // }
        $resultPage->getLayout();
        $listBlock = $resultPage->getLayout()->getBlock('education.collapse');
        //$listBlock->setCurrentPage($currentPage);
        return $this;
    }
}
