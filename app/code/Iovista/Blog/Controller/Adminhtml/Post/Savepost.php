<?php

namespace Iovista\Blog\Controller\Adminhtml\Post;

class Savepost extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    protected $postFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Iovista\Blog\Model\PostFactory $postFactory
    )
    {
        $this->postFactory = $postFactory;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        
        if ($data) {
            // Handle UI component form data structure
            if (isset($data['data']) && is_array($data['data'])) {
                // Merge nested data structure
                $data = array_merge($data, $data['data']);
                unset($data['data']);
            }
            
            // Get post ID if editing
            $id = $this->getRequest()->getParam('id');
            
            try {
                $model = $this->postFactory->create();
                $originalBannerImage = null;
                
                if ($id) {
                    $model->load($id);
                    if (!$model->getId()) {
                        $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                        return $resultRedirect->setPath('*/*/');
                    }
                    // Store the original banner_image value BEFORE we modify any data
                    // This is critical for tracking changes
                    $originalBannerImage = $model->getData('banner_image');
                    // Normalize original path for comparison
                    if ($originalBannerImage) {
                        $originalBannerImage = trim(str_replace('\\', '/', $originalBannerImage), '/');
                    }
                    // Ensure origData is set for banner_image so Magento can track changes
                    if ($originalBannerImage !== null) {
                        $model->setOrigData('banner_image', $originalBannerImage);
                    }
                }
                
                // Clean up data - remove form keys that shouldn't be saved
                unset($data['form_key']);
                unset($data['id']); // Will be set from model if editing
                
                // Map title to name if name field exists in database
                if (isset($data['title']) && !empty($data['title'])) {
                    $data['name'] = $data['title'];
                }
                
                // Set post date if not provided
                if (empty($data['post_date']) && !$id) {
                    $data['post_date'] = date('Y-m-d H:i:s');
                }
                
                // Handle banner_image if it's an array (from file uploader)
                // When image is deleted, imageUploader sends empty array [] or may omit the field
                $imageDeleted = false;
                $newBannerImage = null;
                
                // Check if banner_image exists in POST data
                if (isset($data['banner_image'])) {
                    if (is_array($data['banner_image'])) {
                        // Check if array is empty - empty array [] means image was deleted
                        if (empty($data['banner_image'])) {
                            // Array is empty - image was deleted
                            $newBannerImage = '';
                            $imageDeleted = true;
                        } elseif (isset($data['banner_image'][0]['file']) && !empty($data['banner_image'][0]['file'])) {
                            // Use the file path from the upload (new image uploaded)
                            $newBannerImage = $data['banner_image'][0]['file'];
                            // Normalize the path (remove leading/trailing slashes, normalize separators)
                            $newBannerImage = trim(str_replace('\\', '/', $newBannerImage), '/');
                        } elseif (isset($data['banner_image'][0]['url']) && !empty($data['banner_image'][0]['url'])) {
                            // Extract path from URL (existing image or uploaded image)
                            $url = $data['banner_image'][0]['url'];
                            if (strpos($url, '/media/') !== false) {
                                $newBannerImage = substr($url, strpos($url, '/media/') + 7);
                            } else {
                                $newBannerImage = $url;
                            }
                            // Normalize the path
                            $newBannerImage = trim(str_replace('\\', '/', $newBannerImage), '/');
                        } else {
                            // Array has elements but no valid file/url - treat as deleted
                            $newBannerImage = '';
                            $imageDeleted = true;
                        }
                    } elseif ($data['banner_image'] === '' || $data['banner_image'] === null) {
                        // If it's empty string or null, clear the image
                        $newBannerImage = '';
                        $imageDeleted = true;
                    } else {
                        // It's a string value (path) - keep it
                        $newBannerImage = trim(str_replace('\\', '/', $data['banner_image']), '/');
                    }
                } elseif ($id && $originalBannerImage) {
                    // If editing and banner_image field is not in POST but original exists,
                    // preserve existing value (field might not have been sent)
                    // The imageUploader should always send the field, even if empty
                    // So if it's missing, we'll preserve the existing value
                    $newBannerImage = $originalBannerImage; // Already normalized above
                }
                
                // Set the banner_image value in data array
                // IMPORTANT: If image was deleted, ensure we set it to empty string
                if ($imageDeleted) {
                    $data['banner_image'] = '';
                } elseif ($newBannerImage !== null) {
                    $data['banner_image'] = $newBannerImage;
                } elseif ($id && $originalBannerImage) {
                    // If editing and banner_image not in POST, preserve existing value
                    $data['banner_image'] = $originalBannerImage;
                }
                
                // Remove empty arrays and null values that might cause issues
                foreach ($data as $key => $value) {
                    if ($value === null && in_array($key, ['short_description', 'long_description', 'tags', 'url_key'])) {
                        $data[$key] = '';
                    }
                }
                
                // Set all data to the model
                $model->setData($data);
                
                // If image was deleted, we need special handling to ensure the change is tracked
                if ($imageDeleted) {
                    // If editing, ensure we have the original value for comparison
                    if ($id) {
                        // If we don't have originalBannerImage, get it from the model
                        if (!$originalBannerImage) {
                            $originalBannerImage = $model->getOrigData('banner_image');
                            if ($originalBannerImage) {
                                $originalBannerImage = trim(str_replace('\\', '/', $originalBannerImage), '/');
                            }
                        }
                        // Set origData if we have an original value
                        if ($originalBannerImage) {
                            $model->setOrigData('banner_image', $originalBannerImage);
                        }
                    }
                    
                    // Set to empty string - this will be different from origData, so Magento will track it as a change
                    $model->setData('banner_image', '');
                    // Force the model to recognize this as a change
                    $model->setHasDataChanges(true);
                    
                    // Save the model - this should persist the empty banner_image
                    $model->save();
                    
                    // Failsafe: Direct database update to ensure the field is cleared
                    // This guarantees the deletion even if model save had issues
                    if ($model->getId()) {
                        try {
                            $resource = $model->getResource();
                            $connection = $resource->getConnection();
                            $tableName = $resource->getMainTable();
                            $connection->update(
                                $tableName,
                                ['banner_image' => ''],
                                ['post_id = ?' => $model->getId()]
                            );
                        } catch (\Exception $e) {
                            // Log error but don't break the flow
                            // The model save should have handled it
                        }
                    }
                } else {
                    // Normal case - includes new image upload or no image change
                    // If editing and banner_image has changed (new image uploaded), ensure the change is tracked
                    if ($id && $newBannerImage !== null && $newBannerImage !== $originalBannerImage) {
                        // Force the model to recognize this as a change
                        $model->setHasDataChanges(true);
                    }
                    // Save the model - this will save the new image if one was uploaded
                    $model->save();
                }
                
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
                
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
                if ($id) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
                }
            }
        }
        
        return $resultRedirect->setPath('*/*/');
    }
}