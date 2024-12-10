<?php
namespace Customer\Sell\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\File\Mime;

/**
 * Class FileInfo
 *
 * Provides information about requested file
 */
class FileInfo
{
    /**
     * Path in root directory
     */
    const ENTITY_MEDIA_PATH = 'sell/dimages';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @param Filesystem $filesystem
     * @param Mime $mime
     */
    public function __construct(
        Filesystem $filesystem,
        Mime $mime
    ) {
        $this->filesystem = $filesystem;
        $this->mime = $mime;
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::ROOT);
        }
        return $this->mediaDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     */
    public function getMimeType($fileName)
    {
        if ($this->isExist($fileName)) {
            $filePath = self::ENTITY_MEDIA_PATH . '/' . ltrim($fileName, '/');
            $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);
            $result = $this->mime->getMimeType($absoluteFilePath);
            return $result;
        }
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     */
    public function getStat($fileName)
    {
        if ($this->isExist($fileName)) {
            $filePath = self::ENTITY_MEDIA_PATH . '/' . ltrim($fileName, '/');
            $result = $this->getMediaDirectory()->stat($filePath);
            return $result;

        }
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @return bool
     */
    public function isExist($fileName)
    {
        $filePath = self::ENTITY_MEDIA_PATH . '/' . ltrim($fileName, '/');
        $result = $this->getMediaDirectory()->isExist($filePath);
        return $result;
    }
}
