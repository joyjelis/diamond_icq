<?php
namespace MageArray\Faq\Controller\Adminhtml\Faq;

/**
 * Class NewAction
 * @package MageArray\Faq\Controller\Adminhtml\Faq
 */
class NewAction extends \MageArray\Faq\Controller\Adminhtml\Faq
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
