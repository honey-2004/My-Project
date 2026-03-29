<?php
namespace Iovista\SeoXmlExclude\Plugin;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\ObjectManager;

class ExcludeCategoryFromSitemapPlugin
{
    protected $categoryFactory;

    public function __construct(CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    public function afterGetCollection(
        \Magento\Sitemap\Model\ResourceModel\Catalog\Category $subject,
        array $result
    ) {
        $filtered = [];

        foreach ($result as $categoryRow) {
            if (!isset($categoryRow['id'])) {
                continue;
            }

            $category = $this->categoryFactory->create()->load($categoryRow['id']);

            // Check for our custom attribute
            if (!$category->getData('exclude_from_xml_sitemap')) {
                $filtered[] = $categoryRow;
            }
        }

        return $filtered;
    }
}
