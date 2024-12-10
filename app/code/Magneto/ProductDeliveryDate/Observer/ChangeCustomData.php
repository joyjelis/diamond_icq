<?php
namespace Magneto\ProductDeliveryDate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class ChangeCustomData implements ObserverInterface
{
    protected $layout;
    protected $quoteFactory;
    protected $logger;
    public $homeblock;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\Product $prodModel,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magneto\Custom\Block\Addtocart $homeblock
    ) {
        $this->layout = $layout;
        $this->logger = $logger;
        $this->prodModel = $prodModel;
        $this->quoteRepository = $quoteRepository;
        $this->homeblock = $homeblock;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {

                $item = $observer->getEvent()->getData('quote_item');
                $item->getProduct()->setIsSuperMode(true);

                $quote = $item->getQuote();
                $quoteId = $quote->getId();
               // $logger->info('>>>>>->>> ============== current quote id: '.$quoteId);

                $items = $quote->getAllItems();
                $deliveryDaysArray = [];
                //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $flag = 0;
            foreach ($items as $quote_item) {
                $productDeliveryDays = '';
                $itemsId = $quote_item->getId();
                $productId = $quote_item->getProductId();
                $_product = $quote_item->getProduct();
                $sku = $_product->getSku();
                $stock = json_decode($this->homeblock->getSalableQuantity($sku));
                $this->logger->critical('yes');
                $this->logger->critical($sku);
                $this->logger->critical('qty quotes');
                $this->logger->critical($quote_item->getQty());

                $qty = 0;
                if(array_key_exists(0, $stock))
                  {
                      if(isset($stock[0]->qty))
                      {
                        $qty = (int)$stock[0]->qty;
                      }
                }
                $this->logger->critical('qty product');
                $this->logger->critical($qty);

                if ($quote_item->getQty() > $qty ) {
                    $flag = 1;
                }

                $productDeliveryDays = $_product->getData('product_delivery_days');
                if (trim($productDeliveryDays)!='') {
                    $deliveryDaysArray[] = trim($productDeliveryDays);
                }
            }

            $this->logger->critical('flag');
            $this->logger->critical($flag);

            if ($flag == 1) {
                if (!empty($deliveryDaysArray)) {
                    rsort($deliveryDaysArray);
                    $deliveryDaysArrayFirstIndex = $deliveryDaysArray[0];
                    $block = $this->layout->createBlock('Magneto\ProductDeliveryDate\Block\Product\View::class');
                    $finalDate = $block->getQuoteDeliveryDate($deliveryDaysArrayFirstIndex);
                    if ($quoteId!='') {
                        $quotes = $this->quoteRepository->get($quoteId); // Get quote by id
                        $quotes->setData('estimated_delivery_date', $finalDate); // Fill data
                        $this->quoteRepository->save($quotes); // Save quote
                    }
                } else {
                    if ($quoteId!='') {
                        $quotes = $this->quoteRepository->get($quoteId); // Get quote by id
                        $quotes->setData('estimated_delivery_date', ''); // Fill data
                        $this->quoteRepository->save($quotes); // Save quote
                    }
                }
            }
        } catch (NoSuchEntityException $ex) {
            $this->logger->critical($ex);
        } catch (\Exception $ex) {
            $this->logger->critical($ex);
        }
    }
}
