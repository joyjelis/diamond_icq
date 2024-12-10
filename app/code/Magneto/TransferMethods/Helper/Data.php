<?php

namespace Magneto\TransferMethods\Helper;

use Magento\Customer\Model\SessionFactory;
use Magento\Directory\Block\Data as DirectoryHelper;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magneto\TransferMethods\Model\MethodsFactory;

class Data extends AbstractHelper
{

    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        MethodsFactory $methodsFactory,
        CountryFactory $countryFactory,
        StoreManagerInterface $storeManager,
        SessionFactory $customerSession,
        RequestFactory $requestFactory,
        DirectoryHelper $directoryBlock,
        BlockFactory $blockFactory
    ) {
        $this->directoryBlock = $directoryBlock;
        $this->_countryFactory = $countryFactory;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->methodsFactory = $methodsFactory;
        $this->requestFactory = $requestFactory;
        $this->blockFactory = $blockFactory;
        parent::__construct($context);
    }

    public function getCountryname($countryCode)
    {
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    public function SaveData($data, $id = null)
    {
        if (!$this->_customerSession->create()->getId()) {
            return false;
        }

        $method = $this->methodsFactory->create();

        if ($id) {
            $method = $this->methodsFactory->create()->load($id);
        }

        $data['customer_id'] = $this->_customerSession->create()->getId();
        if ($method) {
            if ($id && $data['action'] == 'delete') {
                $model = $this->methodsFactory->create()->load($id);
                $model->delete();
                return true;
            }
            foreach ($data as $key => $value) {
                $method->setData($key, $value);
            }

            $method->save();
            return true;
        }

        return false;
    }

    public function SaveDataAdmin($data, $id = null)
    {
        $method = $this->methodsFactory->create();

        if ($id) {
            $method = $this->methodsFactory->create()->load($id);
            if ($method) {
                foreach ($data as $key => $value) {
                    $method->setData($key, $value);
                }

                $method->save();
                return true;
            }
        }
        
        return false;
    }

    public function getMethods($page = 40)
    {
        if (!$this->_customerSession->create()->getId()) {
            return false;
        }

        $methods = $this->methodsFactory->create()->getCollection()
            ->addFieldToFilter('customer_id', $this->_customerSession->create()->getId())
            ->setPageSize(4)
            ->setCurPage($page)
            ->setOrder('created_at', "DESC");

        return $this->methodcollection = $methods;
    }

    public function getItemsHtml()
    {
        $block = $this->blockFactory->createBlock(\Magneto\TransferMethods\Block\Methods::class);

        $page_size = array_combine(\Magneto\TransferMethods\Block\Methods::PAGE_LIMIT, \Magneto\TransferMethods\Block\Methods::PAGE_LIMIT);
        $pager = $this->blockFactory->createBlock(\Magento\Theme\Block\Html\Pager::class)->setAvailableLimit($page_size)->setShowPerPage(true)->setCollection($block->getCollection());

        $html = $block->toHtml();
        $pager = $pager->toHtml();
        return ['block' => $html, 'pager' => $pager];
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCountries($defValue = null, $name = 'country', $id = 'country', $title = 'Country')
    {
        $country = $this->directoryBlock->getCountryHtmlSelect($defValue, $name, $id, $title);
        return $country;
    }

    public function getSellitem($sellId)
    {
        if (!$this->_customerSession->create()->getId()) {
            return false;
        }

        $sellItems = $this->methodsFactory->create()->load($sellId);
        return $sellItems;
    }
}
