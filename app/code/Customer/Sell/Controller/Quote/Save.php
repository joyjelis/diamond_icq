<?php declare (strict_types = 1);

namespace Customer\Sell\Controller\Quote;

use Customer\Sell\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class Save extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $post = $this->getRequest()->getPost();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $id = $post['id'];
            unset($post['id']);
            $success = $this->helper->ProcessStatus($id, $post);
            if ($success) {
                $result = $this->resultJsonFactory->create();
                return $result->setData(['success' => true]);
            } else {
                return $result->setData(['success' => false]);
            }
        }

        unset($post['form_key']);

        try {
            $id = $post['id'];
            unset($post['id']);
            $success = $this->helper->ProcessStatus($id, $post);
            if ($success) {
                $this->messageManager->addSuccessMessage(__('Your response submitted.'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
