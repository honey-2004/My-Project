<?php

namespace Iovista\Subcategories\Block;

class Manufacturer extends \Magento\Framework\View\Element\Template
{
    public $categoryFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryFactory = $categoryFactory;
    }

    public function getCurrentCategory()
    {
        return $this->categoryFactory->create()->load(229);
    }
}
