<?php
namespace Travash\Education\Controller\Adminhtml\Education;

/**
 * Class Edit
 * @package Travash\Education\Controller\Adminhtml\Education
 */
class Edit extends \Travash\Education\Controller\Adminhtml\Education
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('education_id');
        $model = $this->_educationFactory->create();
        if ($id) {
            /* @phpstan-ignore-next-line */
            $model->load($id);
            if (!$model->getEducationId()) {
                $this->messageManager->addError(__(
                    'This Education no longer exists.'
                ));
                $this->_redirect('*/*/');
                /* @phpstan-ignore-next-line */
                return;
            }
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('education', $model);
        $this->_view->loadLayout();
         /* @phpstan-ignore-next-line */
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
        /* @phpstan-ignore-next-line */
        return;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Travash_Education::education');
    }
}
