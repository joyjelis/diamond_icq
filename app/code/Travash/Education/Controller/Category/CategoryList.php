<?php
namespace Travash\Education\Controller\Category;

use Magento\Framework\App\Action\Context;

/**
 * Class CategoryList
 * @package Travash\Education\Controller\Category
 */
class CategoryList extends \Magento\Framework\App\Action\Action
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
     * CategoryList constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $_resultPageFactory
     * @param \Travash\Education\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $_resultPageFactory,
        \Travash\Education\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
        $this->resultPageFactory = $_resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $page = $this->resultPageFactory->create(false, ['isIsolated' => true]);
        $pageNo = $this->getRequest()->getParam('p');
        $this->_dataHelper->prepareAndRenderCat($page, $this, $pageNo);

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
