<?php
namespace Travash\Education\Controller\Adminhtml\Educationcat;

use Magento\Backend\App\Action;

/**
 * Class MassStatus
 * @package Travash\Education\Controller\Adminhtml\Educationcat
 */
class MassStatus extends \Magento\Backend\App\Action
{

    /**
     * @return mixed
     */
    public function execute()
    {
        $blogCommentId = $this->getRequest()->getParam('educationcat');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!is_array($blogCommentId) || empty($blogCommentId)) {
            $this->messageManager->addError(__(
                'Please select education category(s).'
            ));
        } else {
            try {
                $status = (int)$this->getRequest()->getParam('status');
                foreach ($blogCommentId as $postId) {
                    $post = $objectManager->get(
                        'Travash\Education\Model\Educationcat'
                    )->load($postId);
                    /* @phpstan-ignore-next-line */
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
