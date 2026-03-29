<?php

namespace Iovista\Subcategories\Block;

class Subcategories extends \Magento\Framework\View\Element\Template
{
    private $layerResolver;
    public $categoryRepository;
    public $categoryFactory;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->layerResolver = $layerResolver;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager= $storeManager;
    }

    public function getCurrentCategory()
    {
        return $this->layerResolver->get()->getCurrentCategory();
    }

    public function getCurrentCategoryId()
    {
        return $this->getCurrentCategory()->getId();
    }

    public function getCategoryData($id)
    {
        return $category = $this->categoryFactory->create()->load($id);
    }
    
    public function getImageUrl($id)
    {
        $category = $this->categoryFactory->create()->load($id);
        return $category->getImageUrl();
    }

    public function getThumbnailImageUrl($id)
    {
        $category = $this->categoryFactory->create()->load($id);
        $image = $category->getThumbnail();
        $thumbnailUrl = "";
        if($image){
            if(strpos($image, "catalog/category") !== false){
                $thumbnailUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).$image;
            } else {
                $thumbnailUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA )."catalog/category/".$image;
            }
        } else {
            $thumbnailUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA )."catalog/category/placeholder/small_image.jpg";
        }
        
        return $thumbnailUrl;
    }
}
