<?php
namespace Magneto\AmastyCustomization\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper {

	protected $_categoryFactory;

	protected $helperImageFactory;

	protected $assetRepos;

	public function __construct(
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
		\Magento\Catalog\Helper\Image $helperImageFactory,
		\Magento\Framework\View\Asset\Repository $assetRepos
	) {
		$this->_categoryFactory = $categoryFactory;
		$this->categoryCollectionFactory = $categoryCollectionFactory;
		$this->helperImageFactory = $helperImageFactory;
		$this->assetRepos = $assetRepos;
	}

	/* Get category object */
	public function getCategory($categoryId) {
		$category = $this->_categoryFactory->create()->load($categoryId);
		return $category;
	}

	/* Get all children categories IDs */
	public function getAllChildren($asArray = false, $categoryId) {
		return $this->getCategory($categoryId)->getAllChildren($asArray);
	}

	/* Get children ids comma separated */
	public function getChildren($categoryId) {
		return $this->getCategory($categoryId)->getChildren();
	}

	public function getAllSubCategoryDetails($categoryId) {
		$allCategoryIds = $this->getChildren($categoryId);
		$subCatData = [];
		if (trim($allCategoryIds) != '') {
			$cats = explode(",", $allCategoryIds);
			foreach ($cats as $childCatId) {
				$categoryInfo = $this->getCategory($childCatId);
				$subCatData[$categoryInfo->getId()] = $categoryInfo->getName();
			}
		}
		return $subCatData;
	}

	/**
     * Get place holder image of a product for small_image
     *
     * @return string
     */
    public function getPlaceHolderImage($category,$size)
    {
        return $this->helperImageFactory->init($category,'category_page_grid')->resize($size, $size)->getUrl();
    }
}
