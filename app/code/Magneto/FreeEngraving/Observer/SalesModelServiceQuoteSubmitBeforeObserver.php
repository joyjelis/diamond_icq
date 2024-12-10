<?php

namespace Magneto\FreeEngraving\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;

class SalesModelServiceQuoteSubmitBeforeObserver implements ObserverInterface
{
    private $quoteItems = [];
    private $quote = null;
    private $order = null;

    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {

       /*
        $logFilePath = BP . '/var/log/SalesModelServiceQuoteSubmitBeforeObserver.log';
        $writer = new \Zend\Log\Writer\Stream($logFilePath);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

       */
        
        $this->quote = $observer->getQuote();
        $this->order = $observer->getOrder();

//        $logger->info('                                                               ');
  //      $logger->info('===============================================================');



        // can not find a equivalent event for sales_convert_quote_item_to_order_item
        /* @var  \Magento\Sales\Model\Order\Item $orderItem */
        foreach ($this->order->getItems() as $orderItem) {
           // $logger->info('getParentItemId : '.$orderItem->getParentItemId());
           // $logger->info('getProductType : '.$orderItem->getProductType());
            
            if (!$orderItem->getParentItemId() && $orderItem->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
             //   $logger->info('getQuoteItemId : '.$orderItem->getQuoteItemId());

                if ($quoteItem = $this->getQuoteItemById($orderItem->getQuoteItemId())) {
 
                    //$logger->info(' --- Come Here 1 --- ');


                    if ($additionalOptionsQuote = $quoteItem->getOptionByCode('additional_options')) {  // $logger->info(' --- Come Here 2 --- ');
                        //To do
                        // - check to make sure element are not added twice
                        // - $additionalOptionsQuote - may not be an array
                        $additionalOptionsQuote = [];
                        if ($additionalOptionsOrder = $orderItem->getProductOptionByCode('additional_options')) {
                            //$logger->info(' --- Come Here 3 --- '.json_encode($additionalOptionsOrder));
                            $additionalOptions = array_merge($additionalOptionsQuote, $additionalOptionsOrder);
                            //$logger->info(' --- Come Here 3.1 --- '.json_encode($additionalOptions));
                        } else {  // $logger->info(' --- Come Here 4 --- ');
                            $additionalOptions = $additionalOptionsQuote;
                        }
                        if (count($additionalOptions) > 0) {   //$logger->info(' --- Come Here 5 --- ');
                            $options = $orderItem->getProductOptions();
                           // print_r($options);exit;
                           // $logger->info(' --- Come Here 5.1 --- '.json_encode($options));
                            //$options['additional_options'] = $this->serializer->unserialize($additionalOptions->getValue());
                            $options['additional_options'] = $additionalOptions;//(array) unserialize($additionalOption->getValue());
                            $orderItem->setProductOptions($options);
                        }

                    }
                }
            }
        }
        //$logger->info('===============================================================');
    }
    private function getQuoteItemById($id)
    {
        if (empty($this->quoteItems)) {
            /* @var  \Magento\Quote\Model\Quote\Item $item */
            if (is_array($this->quote->getItems())) {
                foreach ($this->quote->getItems() as $item) {
                    //filter out config/bundle etc product
                    if (!$item->getParentItemId() && $item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                        $this->quoteItems[$item->getId()] = $item;
                    }
                }
            }
        }
        if (array_key_exists($id, $this->quoteItems)) {
            return $this->quoteItems[$id];
        }
        return null;
    }
}
