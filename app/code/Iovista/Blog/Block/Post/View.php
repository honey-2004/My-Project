<?php

namespace Iovista\Blog\Block\Post;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Iovista\Blog\Model\PostFactory;
use Magento\Framework\UrlInterface;

class View extends Template
{
    protected $postFactory;
    protected $urlBuilder;
    protected $post;

    public function __construct(
        Context $context,
        PostFactory $postFactory,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->postFactory = $postFactory;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    public function getPost()
    {
        if ($this->post === null) {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->post = $this->postFactory->create()->load($id);
            }
        }
        return $this->post;
    }

    public function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return '';
        }
        
        $storeManager = $this->_storeManager;
        $baseUrl = $storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        
        return $baseUrl . ltrim($imagePath, '/');
    }

    public function getBlogUrl()
    {
        return $this->urlBuilder->getUrl('blog');
    }
}






