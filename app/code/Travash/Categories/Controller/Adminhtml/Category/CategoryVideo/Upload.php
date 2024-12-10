<?php
namespace Travash\Categories\Controller\Adminhtml\Category\CategoryVideo;

use Exception;
use Travash\Categories\Model\VideoUploader;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Upload
 */
class Upload extends Action implements HttpPostActionInterface
{
    /**
     * Video uploader
     *
     * @var VideoUploader
     */
    protected $videoUploader;
 
    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param VideoUploader $videoUploader
     */
    public function __construct(
        Context $context,
        VideoUploader $videoUploader
    ) {
        parent::__construct($context);
        $this->videoUploader = $videoUploader;
    }
    /**
     * @return mixed
     */
    public function execute()
    {
        
        $videoId = $this->_request->getParam('param_name', 'category_video');
 
        try {
            $result = $this->videoUploader->saveFileToTmpDir($videoId);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        if (method_exists($this->resultFactory->create(ResultFactory::TYPE_JSON), 'setData')) {
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
        }
        return;
    }
}
