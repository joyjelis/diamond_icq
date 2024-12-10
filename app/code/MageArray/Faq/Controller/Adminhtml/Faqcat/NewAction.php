<?php
namespace MageArray\Faq\Controller\Adminhtml\Faqcat;

/**
 * Class NewAction
 * @package MageArray\Faq\Controller\Adminhtml\Faqcat
 */
class NewAction extends \MageArray\Faq\Controller\Adminhtml\Faqcat
{

    /**
     *
     */
    public function execute()
    {
        $this->_forward('edit');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageArray_Faq::faq');
    }
}
