<?php

namespace Iovista\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Iovista\Blog\Model\PostFactory;
use Magento\Framework\Registry;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Iovista_Blog::post';

    protected $resultPageFactory;
    protected $postFactory;
    protected $coreRegistry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PostFactory $postFactory,
        Registry $coreRegistry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->postFactory = $postFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Iovista_Blog::post');
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->postFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('iovista_blog_post', $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Iovista_Blog::post');
        
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Post'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Post'));
        }
        
        return $resultPage;
    }
}

