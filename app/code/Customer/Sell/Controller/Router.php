<?php
namespace Customer\Sell\Controller;

use Magento\UrlRewrite\Model\OptionProvider;

/**
 * Aureatelabs Custom router Controller Router
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->url = $url;
        $this->_response = $response;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|void
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        if (strpos($identifier, 'diamond-sell') !== false || strpos($identifier, 'sell-diamond') !== false) {
            $url = str_replace("diamond-sell", "sell-diamond", $identifier);
            $url = str_replace("sell-diamond", "sell-your-jewellery", $identifier);
            $this->_response->setRedirect($this->url->getUrl($url), OptionProvider::TEMPORARY);
            $request->setDispatched(true);
            return $this->actionFactory->create('Magento\Framework\App\Action\Redirect');
        }
    }
}
