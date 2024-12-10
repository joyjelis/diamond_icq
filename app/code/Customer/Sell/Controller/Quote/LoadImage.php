<?php
namespace Customer\Sell\Controller\Quote;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlFactory;

// @codingStandardsIgnoreFile

class LoadImage extends \Magento\Framework\App\Action\Action {

	CONST IMAGE = "data:image/gif;base64,R0lGODlhAQABAIAAAP7//wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==";

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\UrlFactory
	 */
	protected $urlFactory;

	/**
	 * @var \Magento\Framework\Controller\Result\RawFactory
	 */
	protected $resultRawFactory;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
	 * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Model\Url $customerUrl,
		\Customer\Sell\Helper\Data $helper,
		\Magento\Framework\Controller\Result\ForwardFactory $forwardFactory,
		UrlFactory $urlFactory
	) {
		$this->helper = $helper;
		$this->_customerSession = $customerSession;
		$this->_customerUrl = $customerUrl;
		$this->resultPageFactory = $resultPageFactory;
		$this->_forwardFactory = $forwardFactory;
		$this->resultRawFactory = $resultRawFactory;
		$this->urlModel = $urlFactory->create();
		parent::__construct($context);
	}

	/**
	 * Dispatch function
	 *
	 * @param RequestInterface $request
	 * @return void
	 */
	public function dispatch(RequestInterface $request) {
		$loginUrl = $this->_customerUrl->getLoginUrl();
		if (!$this->_customerSession->authenticate($loginUrl)) {
			$this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
		}

		return parent::dispatch($request);
	}

	/**
	 * Example for returning Raw Text data
	 *
	 * @return string
	 */
	public function execute() {
		$data = $this->getRequest()->getParam('load');
		if ($data) {
			try {
				$data = json_decode(base64_decode($data));
				$imagedata = null;
				if (isset($data[1]) && isset($data[0])) {
					if ($this->helper->IsValidUser($data[1], $this->_customerSession->getCustomer()->getId())) {
						$imagedata = $this->helper->getFirstImage($data[0]);
						$result = $this->resultRawFactory->create();
						$result->setHeader('Content-type', @mime_content_type($imagedata), true);
						$result->setContents(@file_get_contents($imagedata));
						return $result;
					}
				}
			} catch (Exception $e) {
				$imagedata = $this->helper->getPlaceholderImage();
				$result = $this->resultRawFactory->create();
				$result->setHeader('Content-type', @mime_content_type($imagedata), true);
				$result->setContents(@file_get_contents($imagedata));
				return $result;
			}
		}

		$resultForward = $this->_forwardFactory->create();
		$resultForward->setController('index');
		$resultForward->forward('defaultNoRoute');
		return $resultForward;
	}
}
?>