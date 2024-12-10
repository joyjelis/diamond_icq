<?php
/*
 * @category: Magepow
 * @copyright: Copyright (c) 2014 Magepow (http://www.magepow.com/)
 * @licence: http://www.magepow.com/license-agreement
 * @author: MichaelHa
 * @create date: 2019-11-29 17:19:50
 * @LastEditors: MichaelHa
 * @LastEditTime: 2019-12-04 11:04:26
 */

namespace Magepow\Categories\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

class Categories extends Template implements IdentityInterface
{
    const DEFAULT_CACHE_TAG = 'MAGEPOW_CATEGORIES';

    const XML_PATH = 'category_page';

    const MEDIA_PATH = 'catalog/category';

    const VIDEO_TMP_PATH = 'categories/tmp/video';
 
    const VIDEO_PATH = 'categories/video';
    
    public $helper;

    public $helperImage;

    public $storeManager;

    public $viewAssetRepo;
    
    public $coreRegistry;

    public $categoryFactory;

    public $catalogHelperOutput;

    public $objectManager;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;

    protected $_filesystem;
    
    protected $_directoryRed;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Image $helperImage,
        \Magento\Catalog\Helper\Output $catalogHelperOutput,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magepow\Categories\Helper\Data $helper,
        array $data = []
    ) {
        $this->storeManager        = $storeManager;
        $this->coreRegistry        = $coreRegistry;
        $this->categoryFactory     = $categoryFactory;
        $this->catalogHelperOutput = $catalogHelperOutput;
        $this->helperImage         = $helperImage;
        $this->_imageFactory       = $imageFactory;
        $this->_filesystem         = $context->getFilesystem();
        $this->_directoryRed       = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->helper              = $helper;

        parent::__construct($context, $data);
    }

    protected function getCacheLifetime()
    {
        return parent::getCacheLifetime() ?: 86400;
    }

    public function getCacheKeyInfo()
    {
        $keyInfo     =  parent::getCacheKeyInfo();
        $categoryId  =  $this->getCurrentCategory() ? $this->getCurrentCategory()->getId() : 0;
        $keyInfo[]   =  $categoryId;
        return $keyInfo;
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        $categoryId  = $this->getCurrentCategory() ? $this->getCurrentCategory()->getId() : 0;
        return [self::DEFAULT_CACHE_TAG, self::DEFAULT_CACHE_TAG . '_' . $categoryId];
    }

    public function getLayout()
    {
        return $this->helper->getConfig(self::XML_PATH . '/layout');
    }

    public function getHeading()
    {
        return $this->helper->getConfig(self::XML_PATH . '/heading');
    }

    public function isShowDescription()
    {
        return $this->helper->getConfig(self::XML_PATH . '/description');
    }

    public function isShowThumbnail()
    {
        return $this->helper->getConfig(self::XML_PATH . '/thumbnail');
    }

    public function getItemAmount()
    {
        return $this->helper->getConfig(self::XML_PATH . '/item_amount');
    }
    
    public function getSortAttribute()
    {
        return $this->helper->getConfig(self::XML_PATH . '/sort_attribute');
    }

    public function getExcludeCategory()
    {
        return $this->helper->getConfig(self::XML_PATH . '/exclude_category');
    }

    public function getCurrentCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }

    public function getCategories()
    {
        $category = $this->getCurrentCategory();
        if (!$category) {
            return;
        }

        $categoryId = $category->getId();

        if ($this->isExcluded($categoryId)) {
            return;
        }

        $sortAttribute = $this->getSortAttribute();
        $categories = $this->categoryFactory->create()->getCollection()
                            ->addAttributeToSelect(['name', 'url_key', 'url_path', 'image','description'])
                            ->addAttributeToFilter('parent_id', $categoryId)
                            ->addIsActiveFilter();

        if ($sortAttribute == "position") {
            $categories->addAttributeToSort('level');
        }

        $categories->addAttributeToSort($sortAttribute);

        return $categories;
    }

    public function getFixedCategories()
    {
        $categoriesId = $this->getExcludeCategory();
        $categoriesId = explode(",", $categoriesId);
        $categories = $this->categoryFactory->create()->getCollection()->addIsActiveFilter();
        foreach ($categories as $category) {
            $categoryid[] = $category->getId();
        }

        $categoryIds = array_diff($categoryid, $categoriesId);
        unset($categoryIds[0]);

        $currentCategory = $this->getCurrentCategory();
        $currentCategoryParentId[] = $currentCategory->getParentId();
        $currentCategoryId[] = $currentCategory->getId();
        $categoryIds = array_diff($categoryIds, $currentCategoryId, $currentCategoryParentId);

        $categoryIds = array_values($categoryIds);


        $categories = $this->categoryFactory->create()->getCollection()
                            ->addAttributeToSelect(['name', 'url_key', 'url_path', 'image','description'])
                            ->addAttributeToFilter('entity_id', $categoryIds)
                            ->addIsActiveFilter();
        
        return $categories;
    }
    
    public function getDescription($category)
    {
        $categoryDescription = '';
        $description = $category->getDescription();
        if ($description) {
            $categoryDescription = $this->catalogHelperOutput->categoryAttribute($category, $description, 'description');
        }
        return trim($categoryDescription);
    }

    public function getCategoryData($category)
    {
        if (!$this->objectManager) {
            $this->objectManager = $this->categoryFactory->create();
        }
        $object_manager = $category->load($category->getId());
        return $object_manager->getData();
    }

    public function getVideoUrl($videoUrl)
    {
        return  $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::VIDEO_TMP_PATH . '/' . $videoUrl;
    }

    public function getImage($category)
    {
        if ($this->isShowThumbnail()!=1) {
            return $this->getImageUrl($category);
        } else {
            $id = $category->getId();
            $category = $this->categoryFactory->create();
            $category->load($id);
            $image = ($category->getData('magepow_thumbnail') != null) ? $category->getData('magepow_thumbnail') : '';
        }
        return $this->getImageUrl($image);
    }

    public function getImageInfo($image)
    {
        if (is_object($image)) {
            $img = $image->getImage();
            if (!$img) {
                return $image;
            }
        } else {
            $img = $image;
        }
        $_image  = $this->_imageFactory->create();
        $absPath = $this->_directoryRed->getAbsolutePath() . str_replace('/pub/media/', '', $img);
        if (file_exists($absPath)) {
            $_image->open($absPath);
            return $_image;
        }
        return $image;
    }

    public function getImageUrl($image)
    {
        if (is_object($image)) {
            $image = $image->getImage();
        }
        if (strpos($image  ?? '', "media/")) {
            $image = strstr($image, '/media');
        } elseif ($image!=null) {
            $image = 'catalog/category/'.$image;
        }
        $image = str_replace('media/', '', $image ?? '');

        if ($image) {
            $url = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $image;
        } else {
            $url = $this->helperImage->getDefaultPlaceholderUrl('small_image');
        }

        return $url;
    }
    
    public function isExcluded($id)
    {
        $excluded = explode(',', $this->getExcludeCategory());
        /* @phpstan-ignore-next-line */
        if (!$excluded) {
            return null;
        }
        return in_array($id, $excluded);
    }
}
