<?php
namespace Travash\Education\Controller\Index;

use Magento\Framework\App\Action\Context;

/**
 * Class View
 * @package Travash\Education\Controller\Index
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var string
     */
    protected $viewHelper;
    /**
     * @var \Travash\Education\Helper\Data
     */
    protected $_dataHelper;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * View constructor.
     * @param Context $context
     * @param \Travash\Education\Helper\Data $dataHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Travash\Education\Helper\Data $dataHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $page = $this->resultPageFactory->create();
        return $page;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
