<?php
namespace Travash\Education\Controller\Adminhtml\Education;

use Magento\Backend\App\Action;

/**
 * Class MassStatus
 * @package Travash\Education\Controller\Adminhtml\Education
 */
class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $blogCommentId = $this->getRequest()->getParam('education');
        if (!is_array($blogCommentId) || empty($blogCommentId)) {
            $this->messageManager->addError(__('Please select education(s).'));
        } else {
            try {
                $status = (int)$this->getRequest()->getParam('status');
                foreach ($blogCommentId as $postId) {
                    $post = $this->_objectManager->get(
                        'Travash\Education\Model\Education'
                    )->load($postId);
                    $post->setIsActive($status)->save();
                }
                $this->messageManager->addSuccess(__(
                    'A total of %1 record(s) have been updated.',
                    count($blogCommentId)
                ));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Travash_Education::education');
    }
}
