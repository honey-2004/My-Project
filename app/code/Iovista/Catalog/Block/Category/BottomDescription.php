<?php
declare(strict_types=1);

namespace Iovista\Catalog\Block\Category;

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Helper\Output as CatalogOutputHelper;

class BottomDescription extends Template
{
    private $registry;
    private $catalogOutputHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        CatalogOutputHelper $catalogOutputHelper,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->catalogOutputHelper = $catalogOutputHelper;
        parent::__construct($context, $data);
    }

    public function getBottomDescription(): string
    {
        $category = $this->registry->registry('current_category');
        if (!$category instanceof Category) {
            return '';
        }

        $content = (string)$category->getData('bottom_description');
        if (trim($content) === '') {
            return '';
        }

        return $this->catalogOutputHelper->categoryAttribute(
            $category,
            $content,
            'bottom_description'
        );
    }
}
