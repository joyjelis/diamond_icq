<?php

namespace Customer\Sell\Controller\Adminhtml\Sell;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Adminhtml Category Image Upload Controller
 */
class Upload extends \Magento\Backend\App\Action
{
    /**
     * Image uploader
     *
     * @var \Customer\Sell\Helper\ImageUploader
     */
    protected $imageUploader;

    /**
     * Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * Media directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Upload constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Customer\Sell\Helper\ImageUploader $imageUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Customer\Sell\Helper\ImageUploader $imageUploader,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem\Io\File $fileIo
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->logger = $logger;
        $this->fileIo = $fileIo;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        // return $this->_authorization->isAllowed('Solveda_Brand::category');
        return true;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->imageUploader->saveFileToTmpDir('image');

            $imageName = $result['name'];

            $basePath = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath() . 'sell/tmp/';
            $mediaRootDir = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath() . 'sell/dimages/';

            if (!file_exists($basePath)) {
                mkdir($basePath, 0755, true);
            }

            if (!file_exists($mediaRootDir)) {
                mkdir($mediaRootDir, 0755, true);
            }
            
            // Set image name with new name, If image already exist
            $newImageName = $this->updateImageName($mediaRootDir, $imageName);
            
            $this->fileIo->mv($basePath . $imageName, $mediaRootDir . $newImageName);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];

            $type = pathinfo($mediaRootDir . $newImageName, PATHINFO_EXTENSION);
            $data = file_get_contents($mediaRootDir . $newImageName);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            $result['name'] = $newImageName;
            $result['file'] = $newImageName;
            $result['url'] = $base64;
            $result['encodedURL'] = $base64;
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
    

    public function updateImageName($path, $file_name)
    {
        if ($position = strrpos($file_name, '.')) {
            $name = substr($file_name, 0, $position);
            $extension = substr($file_name, $position);
        } else {
            $name = $file_name;
        }
        $new_file_path = $path . '/' . $file_name;
        $new_file_name = $file_name;
        $count = 0;
        while (file_exists($new_file_path)) {
            $new_file_name = $name . '_' . $count . $extension;
            $new_file_path = $path . '/' . $new_file_name;
            $count++;
        }
        return $new_file_name;
    }
}
