<?php
namespace Travash\Education\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\State;
use Travash\Education\Model\EducationFactory;
use Travash\Education\Model\EducationcatFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Router
 * @package Travash\Education\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{

    /**
     * @var ActionFactory
     */
    protected $actionFactory;
    /**
     * @var ResponseInterface
     */
    protected $_response;
    /**
     * @var boolean
     */
    protected $dispatched;
    /**
     * @var \Travash\Education\Helper\Data
     */
    protected $_dataHelper;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var UrlInterface
     */
    protected $url;
    /**
     * @var EducationFactory
     */
    protected $educationFactory;
    /**
     * @var EducationcatFactory
     */
    protected $educationcatFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ResponseInterface
     */
    protected $response;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param ManagerInterface $eventManager
     * @param UrlInterface $url
     * @param EducationFactory $educationFactory
     * @param EducationcatFactory $educationcatFactory
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param ScopeConfigInterface $scopeConfig
     * @param \Travash\Education\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        UrlInterface $url,
        EducationFactory $educationFactory,
        EducationcatFactory $educationcatFactory,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        ResponseInterface $response,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        \Travash\Education\Helper\Data $dataHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->url = $url;
        $this->_dataHelper = $dataHelper;
        $this->educationFactory = $educationFactory;
        $this->educationcatFactory = $educationcatFactory;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->response = $response;
        $this->request = $request;
        $this->_response = $response;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function match(RequestInterface $request)
    {
        if (!method_exists($request, 'getPathInfo') ||
            !method_exists($request, 'getOriginalPathInfo') ||
            !method_exists($request, 'setAlias') ||
            !method_exists($request, 'setControllerName') ||
            !method_exists($this->response, 'setRedirect')
            ) {
            return null;
        } elseif (!$this->dispatched) {
            $urlKey = trim($request->getOriginalPathInfo(), '/');
            $urlKeyOriginal = trim($request->getOriginalPathInfo(), '/');
            $origUrlKey = $urlKey;
            $condition = new DataObject(
                [
                    'url_key' => $urlKey,
                    'continue' => true
                ]
            );
            $this->eventManager->dispatch(
                'travash_education_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );
            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                 /* @phpstan-ignore-next-line */
                $request->setDispatched(true);
                /* @phpstan-ignore-next-line */
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                );
            }
            if (!$condition->getContinue()) {
                return null;
            }

            $entities = [
                'author' => [
                    'cat_prefix' => $this->_dataHelper->getCategoryUrlPrifix(),
                    'suffix' => $this->_dataHelper->getCategoryUrlSuffix(),
                    'list_key' => $this->_dataHelper->getPageUrl(),
                    'list_action' => 'categorylist',
                    'controller' => 'category',
                    'action' => 'categorylist',
                    'param' => 'id',
                    'factory' => $this->educationFactory,
                ]
            ];
            $urlKeyPart = '';
            foreach ($entities as $entity => $settings) {
                if ($settings['list_key']) {
                    $_splitEdu = explode('/', $urlKeyOriginal);
                    if (count($_splitEdu) > 1 && $_splitEdu[0] == 'education' && $_splitEdu[1] != 'category') {
                        if ($urlKey == $settings['list_key']) {
                            $request->setModuleName('education')
                                ->setControllerName($settings['controller'])
                                ->setActionName($settings['list_action']);
                            $request->setAlias(
                                Url::REWRITE_REQUEST_PATH_ALIAS,
                                $urlKey
                            );
                            $this->dispatched = true;
                            /* @phpstan-ignore-next-line */
                            return $this->actionFactory->create(
                                'Magento\Framework\App\Action\Forward',
                                ['request' => $request]
                            );
                        } elseif (count($_splitEdu) > 2 && $_splitEdu[1] == 'cat') {
                            $request->setModuleName('education')
                                ->setControllerName($settings['controller'])
                                ->setActionName($settings['list_action'])
                                ->setParam('cat', $_splitEdu[count($_splitEdu) - 3])
                                ->setParam('edu', $_splitEdu[count($_splitEdu) - 1]);
                            $request->setAlias(
                                Url::REWRITE_REQUEST_PATH_ALIAS,
                                $urlKeyOriginal
                            );
                            $this->dispatched = true;
                            /* @phpstan-ignore-next-line */
                            return $this->actionFactory->create(
                                'Magento\Framework\App\Action\Forward',
                                ['request' => $request]
                            );
                        } else {
                            $instanceCategory = $this->educationcatFactory->create();
                            $_categoryId = $instanceCategory
                                ->checkUrlKey(
                                    $_splitEdu[1]
                                );
                            if (!$_categoryId) {
                                return null;
                            }
                
                            $instance = $settings['factory']->create();
                            $_eduKey = str_replace('html', '', $_splitEdu[2]);
                            $id = $instance->checkUrlKey(
                                $_eduKey
                            );
                            if (!$id) {
                                return null;
                            }
                            $request->setModuleName('education')
                                ->setControllerName($settings['controller'])
                                ->setActionName($settings['list_action'])
                                ->setParam('cat', $_categoryId)
                                ->setParam('edu', $id);
                            $request->setAlias(
                                Url::REWRITE_REQUEST_PATH_ALIAS,
                                $urlKeyOriginal
                            );
                            $this->dispatched = true;
                            /* @phpstan-ignore-next-line */
                            return $this->actionFactory->create(
                                'Magento\Framework\App\Action\Forward',
                                ['request' => $request]
                            );
                        }
                    }
                }
                $parts = explode('/', $urlKey);
                $categoryPath = false;
                if ($settings['cat_prefix']) {
                    $categoryPrefix = explode('/', $origUrlKey);
                    if ($parts[0] != $settings
                        [
                        'cat_prefix'
                        ] || count(
                            $categoryPrefix
                        ) != 2) {
                        continue;
                    }
                    $urlKeyPart = $categoryPrefix[1];
                    if ($parts[0] == $settings
                        [
                        'cat_prefix'
                        ]
                    ) {
                        $categoryPath = true;
                    }
                }
                if ($categoryPath) {
                    $urlKeyCategory = $urlKeyPart;
                    $instanceCategory = $this->educationcatFactory->create();
                    $categoryId = $instanceCategory
                        ->checkUrlKey(
                            $urlKeyCategory
                        );
                    if (!$categoryId) {
                        return null;
                    }
                    $request->setModuleName('education')
                        ->setControllerName('category')
                        ->setActionName('categorylist')
                        ->setParam('cat', $categoryId);
                    $request->setAlias(
                        Url::REWRITE_REQUEST_PATH_ALIAS,
                        $origUrlKey
                    );
                    /* @phpstan-ignore-next-line */
                    $request->setDispatched(true);
                    $this->dispatched = true;
                    /* @phpstan-ignore-next-line */
                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                    );
                }

                $instance = $settings['factory']->create();
                $id = $instance->checkUrlKey(
                    $urlKey
                );
                if (!$id) {
                    return null;
                }
                $request->setModuleName('education')
                    ->setControllerName('category')
                    ->setActionName('categorylist')
                    ->setParam('id', $id);
                $request->setAlias(
                    Url::REWRITE_REQUEST_PATH_ALIAS,
                    $origUrlKey
                );
                /* @phpstan-ignore-next-line */
                $request->setDispatched(true);
                $this->dispatched = true;
                /* @phpstan-ignore-next-line */
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            }
        }

        return null;
    }
}
