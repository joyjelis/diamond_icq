<?php
namespace Travash\Education\Controller\Adminhtml\Education;

/**
 * Class Delete
 * @package Travash\Education\Controller\Adminhtml\Education
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('education_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $model = $objectManager->create(
                    'Travash\Education\Model\Education'
                );
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__(
                    'The Education has been deleted.'
                ));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['education_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a Education to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Travash_Education::education');
    }
}
