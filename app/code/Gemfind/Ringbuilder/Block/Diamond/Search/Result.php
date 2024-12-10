<?php

namespace Gemfind\Ringbuilder\Block\Diamond\Search;

use Magento\Framework\View\Element\Template\Context;
use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Framework\Session\SessionManagerInterface;

class Result extends AbstractProduct
{   

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
     * Result Per page
     *
     * @var \Gemfind\Ringbuilder\Model\Config\Source\Options\Resultperpage
     */
    protected $_resultperpage;


    /**
     * Result constructor.
     * @param Context $context
     * @param SessionManagerInterface $sessionManager
     * @param Helper $helper
     * @param StoreManagerInterface $storeManager
     * @param CurrencyInterface $localeCurrency
     * @param Resultperpage $resultperpage
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $sessionManager,
        Helper $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Gemfind\Ringbuilder\Model\Config\Source\Options\Diamondresultperpage $resultperpage,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_localeCurrency = $localeCurrency;
        $this->_resultperpage = $resultperpage;
        parent::__construct($context, $sessionManager, $helper, $data);
    }

    /**
     * @return string
     */
    public function getCurrencySymbol() {
        $code = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        if($code == 'USD'){
            return 'US'.$this->_localeCurrency->getCurrency($code)->getSymbol();    
        }
        return $this->_localeCurrency->getCurrency($code)->getSymbol();
    }

    /**
     * @return array
     */
    public function getResultsPerPageOptions()
    {   
        return $this->_resultperpage->getAllOptions();
    }

}
