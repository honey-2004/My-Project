<?php
namespace Iovista\Blog\Model\Post;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Iovista\Blog\Model\ResourceModel\Post\CollectionFactory;
use Iovista\Blog\Model\PostFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;

class DataProvider extends AbstractDataProvider
{
    protected $loadedData;
    protected $request;
    protected $postFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $postCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $postCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get PostFactory instance
     *
     * @return PostFactory
     */
    protected function getPostFactory()
    {
        if ($this->postFactory === null) {
            $this->postFactory = ObjectManager::getInstance()->get(PostFactory::class);
        }
        return $this->postFactory;
    }

    /**
     * Get request object
     *
     * @return RequestInterface
     */
    protected function getRequest()
    {
        if ($this->request === null) {
            $this->request = ObjectManager::getInstance()->get(RequestInterface::class);
        }
        return $this->request;
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return is_array($this->loadedData) ? $this->loadedData : [];
        }

        $this->loadedData = [];
        
        try {
            $request = $this->getRequest();
            if ($request && $this->requestFieldName) {
                $requestId = $request->getParam($this->requestFieldName);
                
                if ($requestId) {
                    // Load the post directly using PostFactory
                    $postFactory = $this->getPostFactory();
                    $post = $postFactory->create();
                    $post->load($requestId);
                    
                    if ($post && $post->getId()) {
                        $postData = $post->getData();
                        
                        // Ensure all required fields have values
                        if (!isset($postData['status'])) {
                            $postData['status'] = 1;
                        }
                        if (!isset($postData['post_date'])) {
                            $postData['post_date'] = $postData['created_at'] ?? date('Y-m-d H:i:s');
                        }
                        
                        // Format banner_image for imageUploader component
                        if (!empty($postData['banner_image'])) {
                            $imagePath = $postData['banner_image'];
                            $objectManager = ObjectManager::getInstance();
                            $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
                            $baseUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                            
                            // Build full URL - handle both relative and absolute paths
                            if (strpos($imagePath, 'http') === 0) {
                                $imageUrl = $imagePath;
                                // Extract relative path from URL
                                if (strpos($imageUrl, '/media/') !== false) {
                                    $imagePath = substr($imageUrl, strpos($imageUrl, '/media/') + 7);
                                }
                            } else {
                                // Ensure path doesn't have leading slash if baseUrl already has it
                                $imagePath = ltrim($imagePath, '/');
                                $imageUrl = $baseUrl . $imagePath;
                            }
                            
                            // Get file name
                            $fileName = basename($imagePath);
                            
                            // Get file size and dimensions
                            $fileSize = 0;
                            $width = 0;
                            $height = 0;
                            $filesystem = $objectManager->get(\Magento\Framework\Filesystem::class);
                            $mediaDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                            $fullPath = $mediaDirectory->getAbsolutePath($imagePath);
                            
                            if ($mediaDirectory->isFile($imagePath)) {
                                $fileSize = $mediaDirectory->stat($imagePath)['size'] ?? 0;
                                
                                // Get image dimensions
                                if (file_exists($fullPath)) {
                                    $imageInfo = @getimagesize($fullPath);
                                    if ($imageInfo !== false) {
                                        $width = $imageInfo[0];
                                        $height = $imageInfo[1];
                                    }
                                }
                            }
                            
                            $postData['banner_image'] = [
                                [
                                    'file' => $imagePath,
                                    'url' => $imageUrl,
                                    'name' => $fileName,
                                    'size' => $fileSize,
                                    'type' => 'image',
                                    'width' => $width,
                                    'height' => $height
                                ]
                            ];
                        } else {
                            $postData['banner_image'] = [];
                        }
                        
                        $this->loadedData[$post->getId()] = $postData;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->loadedData = [];
        }

        // Always return an array
        return is_array($this->loadedData) ? $this->loadedData : [];
    }
}
