<?php
namespace Travash\Education\Controller\Adminhtml\Educationcat;

/**
 * Class NewAction
 * @package Travash\Education\Controller\Adminhtml\Educationcat
 */
class NewAction extends \Travash\Education\Controller\Adminhtml\Educationcat
{

   /**
    * @return mixed
    */
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
