<?php

namespace Iovista\Customization\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context            $context
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
    }

    public function getCategoryNameById($id, $storeId = null)
    {
        $categoryInstance = $this->categoryRepository->get($id, $storeId);

        return $categoryInstance->getName();
    }

    /**
     * get order
     * @return \Magento\Sales\Model\Order order
     * @deprecated since 3.3.0
     */
    public function getOrder()
    {
        if (!$this->registry->registry('current_order')) {
            $this->registry->register('current_order', $this->checkoutSession->getLastRealOrder());
        }
        return $this->registry->registry('current_order');
    }
}