<?php
namespace Travash\Education\Controller\Adminhtml\Education;

/**
 * Class NewAction
 * @package Travash\Education\Controller\Adminhtml\Education
 */
class NewAction extends \Travash\Education\Controller\Adminhtml\Education
{
    public function execute()
    {
        /* @phpstan-ignore-next-line */
        $this->_forward('edit');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Travash_Education::education');
    }
}
