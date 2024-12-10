<?php
namespace Travash\Education\Controller\Adminhtml\Educationcat;

/**
 * Class Delete
 * @package Travash\Education\Controller\Adminhtml\Educationcat
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('education_cat_id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /* @phpstan-ignore-next-line */
        $resultRedirect = $this->_resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $objectManager->create(
                    'Travash\Education\Model\Educationcat'
                );
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__(
                    'The category has been deleted.'
                ));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'education_cat_id' => $id
                    ]
                );
            }
        }
        $this->messageManager->addError(__(
            'We can\'t find a Category to delete.'
        ));

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
