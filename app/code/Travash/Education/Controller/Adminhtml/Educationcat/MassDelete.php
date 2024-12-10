<?php
namespace Travash\Education\Controller\Adminhtml\Educationcat;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 * @package Travash\Education\Controller\Adminhtml\Educationcat
 */
class MassDelete extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $blogPostId = $this->getRequest()->getParam('educationcat');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (!is_array($blogPostId) || empty($blogPostId)) {
            $this->messageManager->addError(__(
                'Please select education category post(s).'
            ));
        } else {
            try {
                foreach ($blogPostId as $postId) {
                    $post = $objectManager->get(
                        'Travash\Education\Model\Educationcat'
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
