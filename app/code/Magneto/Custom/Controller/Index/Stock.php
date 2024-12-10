<?php

namespace Magneto\Custom\Controller\Index;

class Stock extends  \Magento\Framework\App\Action\Action 
{

  protected $resultJsonFactory;
  protected $homeblock;
  public $priceHelper;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magneto\Custom\Block\Addtocart $homeblock,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->homeblock = $homeblock;
        $this->priceHelper  = $priceHelper;
    }

    public function execute()
    {
      $pid = $this->getRequest()->getParam('pid');
      $product = $this->homeblock->getProduct($pid);
      $_finalPrice = $product->getFinalPrice();
      $_price = $product->getPrice();
      $sku = $this->homeblock->getSku($pid);
      $stock = json_decode($this->homeblock->getSalableQuantity($sku));
      $discount = $this->homeblock->getDiscount($pid);
      $qty = 0;
      $res = array();
      if(array_key_exists(0, $stock))
      {
          if(isset($stock[0]->qty))
          {
            $qty = (int)$stock[0]->qty;
          }
      } 

      $res['qty'] = $qty;
      $res['discount'] = $discount;
      $res['finalprice'] = $this->priceHelper->currency($_finalPrice,true,false);
      $res['special_price'] = $this->priceHelper->currency($_price,true,false);;
      echo json_encode($res);   
    }
}
