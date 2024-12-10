<?php
namespace Travash\Education\Controller\Adminhtml\Education;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Travash\Education\Controller\Adminhtml\Education
 */
class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        /* @phpstan-ignore-next-line */
        $resultPage->setActiveMenu('Travash_Education::education');
        /* @phpstan-ignore-next-line */
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        /* @phpstan-ignore-next-line */
        $resultPage->addBreadcrumb(__('Manage Education'), __('Manage Education'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Education'));

        return $resultPage;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Travash_Education::education');
    }
}
