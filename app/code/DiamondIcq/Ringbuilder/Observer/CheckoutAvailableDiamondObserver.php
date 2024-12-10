<?php

namespace DiamondIcq\Ringbuilder\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;

class CheckoutAvailableDiamondObserver implements ObserverInterface
{
    protected $redirect;
    protected $checkoutSession;
    protected $attributeSet;
    protected $ringbuilderHelper;
    protected $messageManager;
    public function __construct(
        RedirectInterface                                 $_redirect,
        \Magento\Checkout\Model\Session                   $_checkoutSession,
        \Magento\Eav\Api\AttributeSetRepositoryInterface  $_attributeSet,
        \Gemfind\Ringbuilder\Helper\Data                  $_ringbuilderHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Message\ManagerInterface       $_messageManager
    ) {
        $this->redirect                    = $_redirect;
        $this->checkoutSession             = $_checkoutSession;
        $this->attributeSet                = $_attributeSet;
        $this->ringbuilderHelper           = $_ringbuilderHelper;
        $this->stockRegistry = $stockRegistry;
        $this->messageManager              = $_messageManager;
    }

    public function execute(EventObserver $observer)
    {
        $quote = $this->checkoutSession->getQuote();
        foreach ($quote->getAllItems() as $item) {
            $product = $item->getProduct();
            $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
            if ($attributeSetRepository->getAttributeSetName() == "Gemfind Diamonds") {
                $stockItem = $this->stockRegistry->getStockItem($product->getId());
                $diamondId = preg_replace('/[^0-9]/', '', $product->getSku());
                $diamondResult = $this->ringbuilderHelper->getDiamondById($diamondId);
                $error = false;
                if (empty($diamondResult['diamondData']) || empty($stockItem->getQty())) {
                    $error = 'The diamond that was sent to you is unfortunately no longer available.';
                } elseif ($this->ringbuilderHelper->isDiamondSold($product->getSku())) {
                    $error = 'A diamond in your shopping cart is no longer available or already sold!';
                }
                if (!empty($error)) {
                    $this->messageManager->addError(__($error));
                    $controller = $observer->getControllerAction();
                    $this->redirect->redirect($controller->getResponse(), 'checkout/cart');
                }
            }
        }
    }
}
