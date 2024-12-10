<?php
namespace MageArray\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 * @package MageArray\Faq\Controller\Adminhtml\Faq
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $blogPostId = $this->getRequest()->getParam('faq');
        if (!is_array($blogPostId) || empty($blogPostId)) {
            $this->messageManager->addError(__(
                'Please select faq post(s).'
            ));
        } else {
            try {
                foreach ($blogPostId as $postId) {
                    $post = $this->_objectManager->get(
                        'MageArray\Faq\Model\Faq'
                    )->load($postId);
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
        return $this->_authorization->isAllowed('MageArray_Faq::faq');
    }
}
