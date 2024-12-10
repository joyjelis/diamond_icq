<?php

namespace Customer\Sell\Controller\Adminhtml\Sell;

use Magento\Framework\Url\DecoderInterface;

class LoadImage extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Customer_Sell::index';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;
    protected $helper;
    protected $resultRawFactory;
    protected $urlDecoder;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Customer\Sell\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        DecoderInterface $urlDecoder
    ) {
        $this->_pageFactory = $pageFactory;
        $this->helper = $helper;
        $this->resultRawFactory = $resultRawFactory;
        $this->urlDecoder = $urlDecoder;
        return parent::__construct($context);
    }

    /**
     * Example for returning Raw Text data
     *
     * @return string
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $fileSystem = $objectManager->get(\Magento\Framework\Filesystem\DriverInterface::class);
        $data = $this->getRequest()->getParam('load');
        if ($data) {
            try {
                $data = json_decode($this->urlDecoder->decode($data));
                $imagedata = null;
                if (isset($data[1]) && isset($data[0])) {
                    if ($this->_isAllowed()) {
                        $imagedata = $this->helper->getFirstImage($data[0]);
                        $result = $this->resultRawFactory->create();
                        $result->setHeader('Content-type', mime_content_type($imagedata), true);
                        $result->setContents($fileSystem->fileGetContents($imagedata));
                        return $result;
                    }
                }
            } catch (Exception $e) {
                $imagedata = $this->helper->getPlaceholderImage();
                $result = $this->resultRawFactory->create();
                $result->setHeader('Content-type', mime_content_type($imagedata), true);
                $result->setContents($fileSystem->fileGetContents($imagedata));
                return $result;
            }
        }

        $resultForward = $this->_forwardFactory->create();
        $resultForward->setController('index');
        $resultForward->forward('defaultNoRoute');
        return $resultForward;
    }

    /**
     * Is the user allowed to view the page.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(static::ADMIN_RESOURCE);
    }
}
