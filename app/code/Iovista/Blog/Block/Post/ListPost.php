<?php

namespace Iovista\Blog\Block\Post;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Iovista\Blog\Model\ResourceModel\Post\Collection;
use Magento\Framework\UrlInterface;

class ListPost extends Template
{
    protected $postCollection;
    protected $urlBuilder;

    protected $postCollectionFactory;

    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        \Iovista\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->postCollectionFactory = $postCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getPosts()
    {
        if ($this->postCollection === null) {
            $collection = $this->postCollectionFactory->create();
            $collection->addFieldToFilter('status', 1); // Only show enabled posts
            $collection->setOrder('post_date', 'DESC');
            $this->postCollection = $collection;
        }
        return $this->postCollection;
    }

    public function getPostUrl($post)
    {
        return $this->urlBuilder->getUrl('blog/index/view', ['id' => $post->getId()]);
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
}

