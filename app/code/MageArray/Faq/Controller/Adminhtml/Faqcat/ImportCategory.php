<?php
namespace MageArray\Faq\Controller\Adminhtml\Faqcat;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package MageArray\Gallery\Controller\Adminhtml\Image
 */
class ImportCategory extends \Magento\Backend\App\Action
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
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('MageArray_Faq::faqcat');
        $resultPage->addBreadcrumb(__('Import'), __('Import'));
        $resultPage->addBreadcrumb(__(
            'Import Categories'
        ), __(
            'Import Categories'
        ));
        $resultPage->getConfig()
            ->getTitle()
            ->prepend(__('Import Categories'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'MageArray_Faq::importcategory'
        );
    }
}
