<?php
namespace Travash\Education\Controller\Adminhtml\Education;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 * @package Travash\Education\Controller\Adminhtml\Education
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $blogPostId = $this->getRequest()->getParam('education');
        if (!is_array($blogPostId) || empty($blogPostId)) {
            $this->messageManager->addError(__(
                'Please select education post(s).'
            ));
        } else {
            try {
                foreach ($blogPostId as $postId) {
                    $post = $objectManager->get(
                        'Travash\Education\Model\Education'
                    )->load($postId);
                    /* @phpstan-ignore-next-line */
                    $post->delete();
                }
                $this->messageManager->addSuccess(__(
                    'A total of %1 record(s) have been deleted.',
                    count($blogPostId)
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
