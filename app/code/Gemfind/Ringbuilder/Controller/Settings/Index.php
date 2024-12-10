<?php

namespace Gemfind\Ringbuilder\Controller\Settings;

class Index extends \Magento\Framework\App\Action\Action
{

	const RING_COOKIE_NAME = 'ringsetting';

    const DIAMOND_COOKIE_NAME = 'diamondsetting';
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Gemfind\Ringbuilder\Helper\Data
     */
    protected $helper;
	
	/**
    * @var \Magento\Framework\Stdlib\CookieManagerInterface
    */
    protected $_cookieManager;

    /**
     * Loadfilter constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Gemfind\Ringbuilder\Helper\Data $helper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Gemfind\Ringbuilder\Helper\Data $helper,
		\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,  
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
		$this->_cookieManager = $cookieManager;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {   
        if (!$this->_helper->isGemfindEnabled()) {
            $this->messageManager->addError(__("Please enable this Extension, go to configuration."));
            $this->_redirect('/');
        }

        if (!$this->_helper->getUsername()) {
            $this->messageManager->addError(__("Please add Gemfind UserID in Extension Configuration"));
            $this->_redirect('/');
        }
		if($this->_cookieManager->getCookie(self::DIAMOND_COOKIE_NAME)){
            //	$this->_redirect('ringbuilder/settings');
			
 
            $this->_cookieManager->deleteCookie(self::RING_COOKIE_NAME);
        }
        
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
