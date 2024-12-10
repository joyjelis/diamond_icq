<?php

namespace Magneto\FreeEngraving\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\SerializerInterface;

class Add extends \Magento\Framework\App\Action\Action
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
        $text = $this->getRequest()->getParam('text');
        $font = $this->getRequest()->getParam('font');
        $fontScriptValue = $this->getRequest()->getParam('fontScriptValue');
        $isEdit = $this->getRequest()->getParam('edit');
        
        $productInfo = $this->_cart->getQuote()->getItemsCollection();
        //$productInfo = $this->_cart->getQuote()->getAllItems(); /*****For All items *****/
        foreach ($productInfo as $item) {
           // echo $item->getId();
            if ($item->getId() == $itemId) {
                $additionalOptions = [];
                if ($additionalOption = $item->getOptionByCode('additional_options')) {
                    $additionalOptions = $this->serializer->unserialize($additionalOption->getValue());
                }

                $additionalOptions = [];
                
                $additionalOptions[] = [
                    'label' => 'Engraving Font',
                    'value' => $font
                ];

                $additionalOptions[] = [
                    'label' => 'Engraving Text',
                    'value' => $text
                ];
                /*
                $additionalOptions[] = [
                    'label' => 'temp_font_script_value',
                    'value' => $fontScriptValue
                ];
                */
         
                if (count($additionalOptions) > 0) {
                    $item->addOption([
                        'product_id' => $item->getProductId(),
                        'code' => 'additional_options',
                        'value' => $this->serializer->serialize($additionalOptions)
                    ]);
                    $item->saveItemOptions();
                    echo "done";
                } else {
                    echo "error";
                }
            }
        }
        $productInfo->save();
    }
}
