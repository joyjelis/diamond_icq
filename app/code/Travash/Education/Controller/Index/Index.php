<?php
namespace Travash\Education\Controller\Index;

use Magento\Framework\App\Action\Context;

/**
 * Class Index
 * @package Travash\Education\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Travash\Education\Helper\Data
     */
    protected $_dataHelper;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Travash\Education\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Travash\Education\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $page = $this->resultPageFactory->create(false, ['isIsolated' => true]);
        $pageNo = $this->getRequest()->getParam('p');
        $this->_dataHelper->prepareAndRender($page, $this, $pageNo);

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
