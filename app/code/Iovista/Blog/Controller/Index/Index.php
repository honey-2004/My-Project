<?php

namespace Iovista\Blog\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Iovista\Blog\Model\ResourceModel\Post\CollectionFactory;

class Index extends Action
{
    protected $resultPageFactory;
    protected $postCollectionFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $postCollectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Blog'));
        
        return $resultPage;
    }
}

