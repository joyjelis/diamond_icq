<?php
namespace Travash\Education\Controller\Adminhtml\Educationcat;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Travash\Education\Controller\Adminhtml\Educationcat
 */
class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

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
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        /* @phpstan-ignore-next-line */
        $resultPage->setActiveMenu('Travash_Education::educationcat');
        /* @phpstan-ignore-next-line */
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        /* @phpstan-ignore-next-line */
        $resultPage->addBreadcrumb(__(
            'Manage Education Category'
        ), __(
            'Manage Education Category'
        ))
        ;
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Manage Education Category'));

        return $resultPage;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Travash_Education::educationcat');
    }
}
