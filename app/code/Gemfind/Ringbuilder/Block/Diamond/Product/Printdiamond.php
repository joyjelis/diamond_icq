<?php

namespace Gemfind\Ringbuilder\Block\Diamond\Product;

class Printdiamond extends \Magento\Framework\View\Element\Template
{   
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var product data
     */
    protected $product;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Currency Interface
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * Edit constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Gemfind\Ringbuilder\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrenc
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Gemfind\Ringbuilder\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ){
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context,$data);
    }

    /**
     * @return Currency Symbol
     */
    public function getCurrencySymbol() {
        $code = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        if($code == 'USD'){
            return 'US'.$this->_localeCurrency->getCurrency($code)->getSymbol();    
        }
        return $this->_localeCurrency->getCurrency($code)->getSymbol();
    }

    /**
     * @return array|Dimaond
     */
    public function getDiamond()
    {
        $id = $this->getRequest()->getParam('id');
        if($id){
            $diamond = (array)$this->helper->getDiamondById($id);
            return $diamond;    
        }
    }
}
