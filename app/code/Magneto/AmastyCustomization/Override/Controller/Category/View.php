<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magneto\AmastyCustomization\Override\Controller\Category;

use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category\Attribute\LayoutUpdateManager;
use Magento\Catalog\Model\Design;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * View a category on storefront. Needs to be accessible by POST because of the store switching.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Catalog\Controller\Category\View {

	public function __construct(
		Context $context,
		Design $catalogDesign,
		Session $catalogSession,
		Registry $coreRegistry,
		StoreManagerInterface $storeManager,
		CategoryUrlPathGenerator $categoryUrlPathGenerator,
		PageFactory $resultPageFactory,
		ForwardFactory $resultForwardFactory,
		Resolver $layerResolver,
		CategoryRepositoryInterface $categoryRepository,
		ToolbarMemorizer $toolbarMemorizer = null,
		?LayoutUpdateManager $layoutUpdateManager = null,
		CategoryHelper $categoryHelper = null,
		LoggerInterface $logger = null
	) {
		parent::__construct(
			$context,
			$catalogDesign,
			$catalogSession,
			$coreRegistry,
			$storeManager,
			$categoryUrlPathGenerator,
			$resultPageFactory,
			$resultForwardFactory,
			$layerResolver,
			$categoryRepository
		);

        $this->layerResolver = $layerResolver;
	}

	/**
	 * Category view action
	 *
	 * @throws NoSuchEntityException
	 */
	public function execute() {

		$result = null;

		if ($this->_request->getParam(ActionInterface::PARAM_NAME_URL_ENCODED)) {
			return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl());
		}

		$category = $this->_initCategory();
		if ($category) {
			if (!empty($this->_request->getParam('q'))) {
				$this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
			} else {
				$this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
			}

			$settings = $this->_catalogDesign->getDesignSettings($category);

			// apply custom design
			if ($settings->getCustomDesign()) {
				$this->_catalogDesign->applyCustomDesign($settings->getCustomDesign());
			}

			$this->_catalogSession->setLastViewedCategoryId($category->getId());

			$page = $this->resultPageFactory->create();
			// apply custom layout (page) template once the blocks are generated
			if ($settings->getPageLayout()) {
				$page->getConfig()->setPageLayout($settings->getPageLayout());
			}

			$pageType = $this->getPageType($category);

			if (!$category->hasChildren()) {
				// Two levels removed from parent.  Need to add default page type.
				$parentPageType = strtok($pageType, '_');
				$page->addPageLayoutHandles(['type' => $parentPageType], null, false);
			}
			$page->addPageLayoutHandles(['type' => $pageType], null, false);
			$page->addPageLayoutHandles(['displaymode' => strtolower($category->getDisplayMode())], null, false);
			$page->addPageLayoutHandles(['id' => $category->getId()]);

			// apply custom layout update once layout is loaded
			$this->applyLayoutUpdates($page, $settings);

			$page->getConfig()->addBodyClass('page-products')
				->addBodyClass('categorypath-' . $this->categoryUrlPathGenerator->getUrlPath($category))
				->addBodyClass('category-' . $category->getUrlKey());

			return $page;
		} elseif (!$this->getResponse()->isRedirect()) {
			$result = $this->resultForwardFactory->create()->forward('noroute');
		}

		return $result;
	}

    /**
     * Get page type based on category
     *
     * @param Category $category
     * @return string
     */
    private function getPageType(Category $category) : string
    {
        $hasChildren = $category->hasChildren();
        if ($category->getIsAnchor()) {
            return  $hasChildren ? 'layered' : 'layered_without_children';
        }

        return $hasChildren ? 'default' : 'default_without_children';
    }

    /**
     * Apply custom layout updates
     *
     * @param Page $page
     * @param DataObject $settings
     * @return void
     */
    private function applyLayoutUpdates(
        $page,
        $settings
    ) {
        $layoutUpdates = $settings->getLayoutUpdates();
        if ($layoutUpdates && is_array($layoutUpdates)) {
            foreach ($layoutUpdates as $layoutUpdate) {
                $page->addUpdate($layoutUpdate);
                $page->addPageLayoutHandles(['layout_update' => sha1($layoutUpdate)], null, false);
            }
        }

        //Selected files
        if ($settings->getPageLayoutHandles()) {
            $page->addPageLayoutHandles($settings->getPageLayoutHandles());
        }
    }
}