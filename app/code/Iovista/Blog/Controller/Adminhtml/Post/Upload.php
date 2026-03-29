<?php

namespace Iovista\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;

class Upload extends Action
{
    protected $uploaderFactory;
    protected $filesystem;
    protected $storeManager;

    public function __construct(
        Action\Context $context,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => 'banner_image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $basePath = 'iovista/blog/banner';
            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            
            // Create directory if it doesn't exist
            $mediaDirectory->create($basePath);

            $mediaDir = $mediaDirectory->getAbsolutePath($basePath);
            $result = $uploader->save($mediaDir);

            if (!$result) {
                throw new \Exception('File was not uploaded.');
            }

            // Build the file path relative to media directory
            // When dispersion is enabled, result['file'] contains the filename with subdirectory (e.g., "a/b/filename.jpg")
            $fileName = isset($result['file']) ? $result['file'] : '';
            
            // Construct the full relative path from media root
            // The file was saved to $mediaDir, and result['file'] is relative to that directory
            $filePath = rtrim($basePath, '/') . '/' . ltrim($fileName, '/');
            $filePath = str_replace('\\', '/', $filePath);
            $filePath = ltrim($filePath, '/');
            
            // Get absolute path for file operations
            $absolutePath = $mediaDirectory->getAbsolutePath($filePath);
            
            // Verify file exists - if not, try using result['path'] if available
            if (!file_exists($absolutePath) && isset($result['path']) && isset($result['file'])) {
                $absolutePath = rtrim($result['path'], '/') . '/' . ltrim($result['file'], '/');
                // Recalculate filePath from absolute path
                $mediaRoot = $mediaDirectory->getAbsolutePath();
                $filePath = str_replace($mediaRoot, '', $absolutePath);
                $filePath = ltrim(str_replace('\\', '/', $filePath), '/');
            }
            
            // Remove 'path' from result as Magento core does (after we've used it)
            unset($result['path']);
            
            // Get base media URL
            $baseMediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $imageUrl = $baseMediaUrl . $filePath;

            // Get file size
            $fileSize = isset($result['size']) ? $result['size'] : 0;
            if ($fileSize == 0 && file_exists($absolutePath)) {
                $fileSize = filesize($absolutePath);
            }

            // Get image dimensions
            $width = 0;
            $height = 0;
            if (file_exists($absolutePath)) {
                $imageInfo = @getimagesize($absolutePath);
                if ($imageInfo !== false) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            }

            // Prepare response matching Magento core ImageUploader format
            // Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
            $tmpName = isset($result['tmp_name']) ? str_replace('\\', '/', $result['tmp_name']) : '';
            
            // Build response exactly like Magento core ImageUploader::saveFileToTmpDir
            // The imageUploader component expects this format
            $response = [
                'file' => $filePath,
                'name' => isset($result['name']) ? $result['name'] : $fileName,
                'size' => $fileSize,
                'url' => $imageUrl,
                'type' => isset($result['type']) ? $result['type'] : 'image',
                'tmp_name' => $tmpName
            ];
            
            // Add dimensions if available (for preview display)
            if ($width > 0 && $height > 0) {
                $response['width'] = $width;
                $response['height'] = $height;
            }

            // Return response - imageUploader component expects array format
            // The component processes this and converts it to the format it needs
            return $this->resultFactory->create(
                \Magento\Framework\Controller\ResultFactory::TYPE_JSON
            )->setData($response);

        } catch (\Exception $e) {
            return $this->resultFactory->create(
                \Magento\Framework\Controller\ResultFactory::TYPE_JSON
            )->setData([
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ]);
        }
    }
}
