<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace VimirLab\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as ModelContext;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryFactory;
use Magento\Sales\Model\OrderFactory;

class Data extends AbstractHelper
{
    protected $_httpContext;
    protected $_customerSession;
    protected $_registry;
    protected $_productRepository;
    protected $_categoryFactory;
    protected $_orderFactory;
    
    /**
     * Helper Constructor
     * @param Context $context
     * @param HttpContext $httpContext
     * @param CustomerSession $customerSession
     * @param Registry $registry
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryFactory $categoryFactory
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        CustomerSession $customerSession,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        CategoryFactory $categoryFactory,
        OrderFactory $orderFactory
    ) {
        parent::__construct($context);
        $this->_httpContext = $httpContext;
        $this->_customerSession = $customerSession;
        $this->_registry = $registry;
        $this->_productRepository = $productRepository;
        $this->_categoryFactory = $categoryFactory;
        $this->_orderFactory = $orderFactory;
    }
    
    /**
     * Get Customer Name
     * return string|null
     */
    public function getCustomerName()
    {
        $isLoggedIn = $this->_httpContext->getValue(ModelContext::CONTEXT_AUTH);
        if ($isLoggedIn) {
            return $this->_customerSession->getCustomer()->getFirstname();
        }
        return null;
    }
    
    /**
     * Get Current Product
     * return mixed
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
    
    /**
     * Get Category Collection
     *
     * @param $categoryIds
     * return array|null
     */
    public function getCategoryCollection($categoryIds)
    {
        $collection = $this->_categoryFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('entity_id', $categoryIds);
        return $collection;
    }
    
    /**
     * Get Order Information
     *
     * @param $orderId
     * return array|null
     */
    public function getOrderInformation($orderId)
    {
        if ($orderId) {
            return $this->_orderFactory->create()->loadByIncrementId($orderId);
        }
        return null;
    }
}
