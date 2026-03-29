<?php
declare(strict_types=1);

namespace Iovista\Canonical\Helper;

use Magento\Cms\Model\Page;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\UrlInterface;

class Canonical extends AbstractHelper
{
    /**
     * @var Page
     */
    protected $cmsPage;

    /**
     * @var Http
     */
    protected $http;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * Canonical constructor.
     * @param Context $context
     * @param Page    $cmsPage
     * @param Http    $http
     */
    public function __construct(
        Context $context,
        Page $cmsPage,
        Http $http,
        UrlInterface $urlInterface
    ) {
        $this->cmsPage = $cmsPage;
        $this->http = $http;
        $this->urlInterface = $urlInterface;
        parent::__construct($context);
    }

    /**
     * This method is used in XML layout.
     * @return string
     */
    public function getCanonicalForAllCmsPages(): string
    {
        if($this->scopeConfig->getValue('catalog/seo/cms_canonical_tag')){
            if ($this->cmsPage->getId()) {
                if ($this->cmsPage->getIdentifier() === 'home') {
                    return $this->createLink($this->scopeConfig->getValue('web/secure/base_url'));
                } else {
                    return $this->createLink(
                        $this->scopeConfig->getValue('web/secure/base_url') . $this->cmsPage->getIdentifier()
                    );
                }
            }
            $checkModule = $this->http->getModuleName();
            if($checkModule == 'contact'){
                return $this->createLink(
                    $this->scopeConfig->getValue('web/secure/base_url') . $this->http->getModuleName()
                );
            }
        }
        
        if($this->http->getFullActionName() == 'aw_blog_author_list'){
            return $this->createLink(
                $this->urlInterface->getCurrentUrl()
            );
        }

        return '';
    }

    /**
     * @param $url
     * @return string
     */
    protected function createLink($url): string
    {
        return '<link rel="canonical" href="' . $url . '" />';

    }
}