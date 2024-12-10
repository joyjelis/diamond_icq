<?php 

namespace Gemfind\Ringbuilder\Plugin;

use Magento\Framework\Exception\StateException;

class SaveConfigPlugin
{	
	 /**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * AroundSaveConfig constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     */
    public function __construct(
        \Gemfind\Ringbuilder\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }
    
    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        \Closure $proceed
    ) {
        //Proceed
        $returnValue = $proceed();

    	$section = $subject->getSection();
        if($section == 'gemfindringbuilder'){
            $oldConfigs = $this->scopeConfig->getValue($section);
            $curl = curl_init();
            $requestUrl = $oldConfigs['general']['getfilterapi'].'DealerID='.$oldConfigs['general']['username'];
            curl_setopt($curl, CURLOPT_URL, $requestUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->helper->getApiTimeout());
            $responce = curl_exec($curl);
            $results = (array) json_decode($responce);
            if($results[0]->message == 'Invalid Dealer'){
                curl_close($curl);
                $this->configWriter->save('gemfindringbuilder/general/enable_in_frontend',  0, 'default', 0);
                $this->configWriter->save('gemfindringbuilder/general/username', '', 'default', 0);
                foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                    $cacheFrontend->getBackend()->clean();
                }
                throw new StateException(__('Invalid Dealer, Please insert valid UserID.'));
            }
            curl_close($curl);
        }
        return;
    }
}

