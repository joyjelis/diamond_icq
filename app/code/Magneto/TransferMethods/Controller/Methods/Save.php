<?php declare (strict_types = 1);

namespace Magneto\TransferMethods\Controller\Methods;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magneto\TransferMethods\Helper\Data;

class Save extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $is_ajax = false;

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
        try {
            if (isset($post['method_id']) && !empty($post['method_id'])) {
                $id = $post['method_id'];
                $success = $this->helper->SaveData($post, $id);
                $msg = __('Transfer Method Updated.');
                if ($post['action'] == 'delete') {
                    $msg = __('Transfer Method Deleted.');
                }
            } else {
                unset($post['method_id']);
                $success = $this->helper->SaveData($post);
                $msg = __('Transfer Method Added.');
            }

            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->is_ajax = true;
            }

            if ($success) {
                $this->messageManager->addSuccessMessage($msg);
                if ($this->is_ajax) {
                    $result = $this->resultJsonFactory->create();
                    return $result->setData(['success' => true]);
                }

                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;

            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        if ($this->is_ajax) {
            $result = $this->resultJsonFactory->create();
            return $result->setData(['success' => false]);
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
