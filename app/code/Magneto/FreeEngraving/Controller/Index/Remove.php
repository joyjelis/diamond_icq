<?php

namespace Magneto\FreeEngraving\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\SerializerInterface;

class Remove extends \Magento\Framework\App\Action\Action
{

     /**
      * @var Magento\Framework\View\Result\PageFactory
      */
    protected $resultPageFactory;

    protected $resultJsonFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Cart $cart,
        SerializerInterface $serializer
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_cart = $cart;
        $this->serializer = $serializer;
        parent::__construct($context);
    }


    public function execute()
    {
        $itemId = $this->getRequest()->getParam('itemId');
        
        $productInfo = $this->_cart->getQuote()->getItemsCollection();
        //$productInfo = $this->_cart->getQuote()->getAllItems(); /*****For All items *****/
        foreach ($productInfo as $item) {
           // echo $item->getId();
            if ($item->getId() == $itemId) {
                $additionalOptions = [];
                if ($additionalOption = $item->getOptionByCode('additional_options')) {
                    $additionalOptions = $this->serializer->unserialize($additionalOption->getValue());
                }

                $days = $date = '';
                foreach ($additionalOptions as $key => $value) {

                    if ($value['label'] == 'Dispatch Days') {
                        $days = $value['value'];
                    }

                    if ($value['label'] == 'Dispatch Date') {
                        $date = $value['value'];
                    }


                    if ($value['label'] == 'Engraving Font' || $value['label'] == 'Engraving Text') {
                        $additionalOptions = [];
                    }
                }

                if ($days != '') {
                    $additionalOptions[] = [
                        'label' => 'Dispatch Days',
                        'value' => $days
                    ];
                }
                
                if ($date != '') {
                    $additionalOptions[] = [
                        'label' => 'Dispatch Date',
                        'value' => $date
                    ];
                }

                // if (count($additionalOptions) == 0) {
                    $item->addOption([
                        'product_id' => $item->getProductId(),
                        'code' => 'additional_options',
                        'value' => $this->serializer->serialize($additionalOptions)
                    ]);
                    $item->saveItemOptions();
                    echo "done";
                // } else {
                //     echo "error";
                // }
            }
        }
        $productInfo->save();
    }
}
