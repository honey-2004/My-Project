<?php
namespace Iovista\CustomCanonical\Block\Seo;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class Canonical extends Template
{
    protected $registry;
    protected $urlBuilder;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $data);
    }

    public function getCanonicalUrl()
    {
        $category = $this->registry->registry('current_category');

        if ($category && $category->getData('canonical_tag')) {
            $tag = ltrim($category->getData('canonical_tag'), '/');
            return $this->urlBuilder->getBaseUrl() . $tag;
        }

        return $category ? $category->getUrl() : $this->getUrl('');
    }
}
