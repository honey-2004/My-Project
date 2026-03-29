<?php
/**
 * Magenizr NewsletterSubscriptionAtCheckout
 *
 * @category    Magenizr
 * @package     Magenizr_NewsletterSubscriptionAtCheckout
 * @copyright   Copyright (c) 2018 Magenizr (http://www.magenizr.com)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magenizr\NewsletterSubscribeAtCheckout\Model\Plugin\Checkout;

use Magento\Quote\Model\QuoteRepository as QuoteRepository;
use Magenizr\NewsletterSubscribeAtCheckout\Helper\Data as Helper;
use \Psr\Log\LoggerInterface;

/**
 * Class PaymentInformationManagement
 * @package Magenizr\NewsletterSubscribeAtCheckout\Model\Plugin\Checkout
 */
class PaymentInformationManagement
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PaymentInformationManagement constructor.
     * @param QuoteRepository $quoteRepository
     * @param Helper $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Helper $helper,
        LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $subject
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     */
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        // Check if feature is enabled
        if ($this->helper->getConfig('enabled')) {
            try {
                $quote = $this->quoteRepository->getActive($cartId);
                
                // Get newsletter subscription value from multiple sources
                $newsletterSubscribe = false;
                
                // Try to get from request parameters
                if (isset($_POST['newsletter_subscribe'])) {
                    $newsletterSubscribe = (bool)$_POST['newsletter_subscribe'];
                } elseif (isset($_GET['newsletter_subscribe'])) {
                    $newsletterSubscribe = (bool)$_GET['newsletter_subscribe'];
                }
                
                // Try to get from payment method additional data
                if (!$newsletterSubscribe && $paymentMethod->getAdditionalData()) {
                    $additionalData = $paymentMethod->getAdditionalData();
                    
                    // Handle both string and array formats
                    if (is_string($additionalData)) {
                        $additionalData = json_decode($additionalData, true);
                    } elseif (is_array($additionalData)) {
                        // Already an array, use as is
                        $additionalData = $additionalData;
                    } else {
                        $additionalData = [];
                    }
                    
                    if (is_array($additionalData) && isset($additionalData['newsletter_subscribe'])) {
                        $newsletterSubscribe = (bool)$additionalData['newsletter_subscribe'];
                        $this->logger->info('Newsletter subscription from additional_data: ' . var_export($newsletterSubscribe, true));
                    }
                }
                
                // If not found in request, check if there's a current value in quote
                if (!$newsletterSubscribe) {
                    $currentValue = $quote->getNewsletterSubscribe();
                    $newsletterSubscribe = ($currentValue === true || $currentValue === 1 || $currentValue === '1');
                    $this->logger->info('Newsletter subscription from quote: ' . var_export($newsletterSubscribe, true));
                }
                $this->logger->info('Final newsletter subscription value: ' . var_export($newsletterSubscribe, true));
                
                // Convert to integer for database storage
                $newsletterSubscribeValue = $newsletterSubscribe ? 1 : 0;
                
                $quote->setNewsletterSubscribe($newsletterSubscribeValue);
                
                // Save the quote to ensure the value is persisted
                $this->quoteRepository->save($quote);
                $this->logger->info('Saved newsletter subscription value ' . $newsletterSubscribeValue . ' to quote ID: ' . $quote->getId());
                
            } catch (\Exception $e) {
                $this->logger->error('Error saving newsletter subscription to quote during payment: ' . $e->getMessage());
                $this->logger->error('Stack trace: ' . $e->getTraceAsString());
            }
        }
    }
} 