<?php
namespace MageArray\Faq\Controller\Adminhtml\Faqcat;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package MageArray\Faq\Controller\Adminhtml\Faqcat
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
        $resultPage->setActiveMenu('MageArray_Faq::faqcat');
        $resultPage->addBreadcrumb(__('CMS'), __('CMS'));
        $resultPage->addBreadcrumb(__(
            'Manage FAQ Category'
        ), __(
            'Manage FAQ Category'
        ));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Manage FAQ Category'));

        return $resultPage;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageArray_Faq::faqcat');
    }
}
