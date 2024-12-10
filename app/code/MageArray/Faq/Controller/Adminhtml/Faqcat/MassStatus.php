<?php
namespace MageArray\Faq\Controller\Adminhtml\Faqcat;

use Magento\Backend\App\Action;

/**
 * Class MassStatus
 * @package MageArray\Faq\Controller\Adminhtml\Faqcat
 */
class MassStatus extends \Magento\Backend\App\Action
{

    /**
     * @return mixed
     */
    public function execute()
    {
        $blogCommentId = $this->getRequest()->getParam('faqcat');
        if (!is_array($blogCommentId) || empty($blogCommentId)) {
            $this->messageManager->addError(__(
                'Please select faq category(s).'
            ));
        } else {
            try {
                $status = (int)$this->getRequest()->getParam('status');
                foreach ($blogCommentId as $postId) {
                    $post = $this->_objectManager->get(
                        'MageArray\Faq\Model\Faqcat'
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
        return $this->_authorization->isAllowed('MageArray_Faq::faq');
    }
}
