<?php

declare (strict_types = 1);

namespace Customer\Sell\Controller\Adminhtml\Sell;

use Customer\Sell\Helper\Data as QuoteHelper;
use Exception;

class InlineEdit extends \Magento\Backend\App\Action
{

    protected $jsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        QuoteHelper $helper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->helper = $helper;
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $error = true;
        $messages = __('Please correct the data sent.');
        $resultJson = $this->jsonFactory->create();

        if ($this->getRequest()->getParam('isAjax')) {
            $sellItems = $this->getRequest()->getParam('items', []);
            if (count($sellItems)) {
                foreach (array_keys($sellItems) as $modelid) {
                    /** @var \Customer\Sell\Model\Sell $model */
                    $model = $this->_objectManager->create(\Customer\Sell\Model\Sell::class)->load($modelid);
                    try {
                        $data = array_merge($model->getData(), $sellItems[$modelid]);
                        $this->helper->SaveData($modelid, $data);
                        $error = false;
                        $messages = "You saved the Sell.";
                    } catch (Exception $e) {
                        $messages = "[Sell ID: {$modelid}]  {$e->getMessage()}";
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }
}
