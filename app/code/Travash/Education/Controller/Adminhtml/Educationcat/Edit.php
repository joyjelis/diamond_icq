<?php
namespace Travash\Education\Controller\Adminhtml\Educationcat;

/**
 * Class Edit
 * @package Travash\Education\Controller\Adminhtml\Educationcat
 */
class Edit extends \Travash\Education\Controller\Adminhtml\Educationcat
{

    /**
     * @return mixed
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('education_cat_id');
        $model = $this->_educationcatFactory->create();

        if ($id) {
            /* @phpstan-ignore-next-line */
            $model->load($id);
            if (!$model->getEducationCatId()) {
                $this->messageManager->addError(__(
                    'This Category no longer exists.'
                ));
                $this->_redirect('*/*/');
                return;
            }
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('educationcat', $model);
        $this->_view->loadLayout();
        if (method_exists($this->_view->getLayout(), 'initMessages')) {
            $this->_view->getLayout()->initMessages();
        }
        /* @phpstan-ignore-next-line */
        $this->_view->renderLayout();
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Travash_Education::education');
    }
}
