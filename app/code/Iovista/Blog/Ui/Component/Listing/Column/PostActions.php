<?php
namespace Iovista\Blog\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class PostActions extends Column
{
    /** Url path */
    const BLOG_URL_PATH_EDIT = 'iovistablog/post/edit';
    const BLOG_URL_PATH_DELETE = 'iovistablog/post/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var BackendUrlInterface
     */
    protected $backendUrlBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param BackendUrlInterface $backendUrlBuilder
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        BackendUrlInterface $backendUrlBuilder,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->backendUrlBuilder = $backendUrlBuilder;
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['post_id'])) {
                    if (!isset($item[$name])) {
                        $item[$name] = [];
                    }
                    
                    // View action - opens frontend in new tab
                    try {
                        $store = $this->storeManager->getStore();
                        // Build frontend URL using store's base URL
                        $baseUrl = rtrim($store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB), '/');
                        $frontendUrl = $baseUrl . '/blog/index/view/id/' . $item['post_id'];
                        $item[$name]['view'] = [
                            'href' => $frontendUrl,
                            'label' => __('View'),
                            'target' => '_blank',
                            'hidden' => false,
                            'class' => 'action-menu-item'
                        ];
                    } catch (\Exception $e) {
                        // If store manager fails, skip view action
                    }
                    
                    // Edit action
                    $item[$name]['edit'] = [
                        'href' => $this->backendUrlBuilder->getUrl(
                            self::BLOG_URL_PATH_EDIT,
                            ['id' => $item['post_id']]
                        ),
                        'label' => __('Edit'),
                        'hidden' => false,
                        'class' => 'action-menu-item'
                    ];
                    
                    // Delete action
                    $item[$name]['delete'] = [
                        'href' => $this->backendUrlBuilder->getUrl(
                            self::BLOG_URL_PATH_DELETE,
                            ['id' => $item['post_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete Post'),
                            'message' => __('Are you sure you want to delete this post?')
                        ],
                        'hidden' => false,
                        'class' => 'action-menu-item',
                        'post' => true,
                    ];
                }
            }
        }
        return $dataSource;
    }
}

