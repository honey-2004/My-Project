<?php
/**
 * Magenizr NewsletterSubscriptionAtCheckout
 *
 * @category    Magenizr
 * @package     Magenizr_NewsletterSubscriptionAtCheckout
 * @copyright   Copyright (c) 2018 Magenizr (http://www.magenizr.com)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magenizr\NewsletterSubscribeAtCheckout\Model\Config\Source;

use Magenizr\NewsletterSubscribeAtCheckout\Helper\Data as Helper;
use Magento\Customer\Model\Session;
use Magento\Newsletter\Model\SubscriberFactory;

/**
 * Class NewsletterSubscribeLayoutProcessor
 * @package Magenizr\NewsletterSubscribeAtCheckout\Model\Config\Source
 */
class NewsletterSubscribeLayoutProcessor
{
    /**
     * @var Helper
     */
    private $helper;
    private $customerSession;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;
    /**
     * NewsletterSubscribeLayoutProcessor constructor.
     * @param Helper $helper
     */
   public function __construct(
        Helper $helper,
        Session $customerSession,
        SubscriberFactory $subscriberFactory
    ) {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $enabled = ($this->helper->getConfig('enabled')) ? 1 : 0;
        $checked = ($this->helper->getConfig('checked')) ? 1 : 0;
        $label = $this->helper->getConfig('label');
        $note_enabled = ($this->helper->getConfig('note_enabled')) ? 1 : 0;
        $note = $this->helper->getConfig('note');

        $isSubscribed = false;
        if ($this->customerSession->isLoggedIn()) {
            $customerEmail = $this->customerSession->getCustomerData()->getEmail();
            $customerId = $this->customerSession->getCustomerId();

            if($customerEmail) {
                $subscriber = $this->subscriberFactory->create()->loadByEmail($customerEmail);
            }else{
                $subscriber = $this->subscriberFactory->create()->loadByCustomerId($customerId);
            }

            if ($subscriber && $subscriber->getStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED) {
                $isSubscribed = true;  
            }
        }

        if ($isSubscribed) {
            $enabled = 0;
            $checked = 1; 
        }
        $config = [
             'newsletter_subscribe' => [
                'config' => [
                    'enabled' => $enabled,
                    'checked' => $checked,
                    'label' => $label,
                    'note_enabled' => $note_enabled,
                    'note' => $note
                ]
            ]
        ];

        $updateLayout = [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'billing-step' => [
                                    'children' => [
                                        'payment' => [
                                            'children' => [
                                                'payments-list' => [
                                                    'children' => $config
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'shipping-step' => [
                                    'children' => [
                                        'shippingAddress' => [
                                            'children' => $config
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Merge updated layout with existing one
        return array_merge_recursive($jsLayout, $updateLayout);
    }
}
