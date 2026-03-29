/**
 * Magenizr NewsletterSubscriptionAtCheckout
 *
 * @category    Magenizr
 * @package     Magenizr_NewsletterSubscriptionAtCheckout
 * @copyright   Copyright (c) 2018 Magenizr (http://www.magenizr.com)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

define([
    'jquery',
    'Magento_Checkout/js/model/payment/additional-validators'
], function ($, additionalValidators) {
    'use strict';

    return function (Component) {
        return Component.extend({
            /**
             * Initialize the component
             */
            initialize: function () {
                this._super();
                
                // Ensure paymentData is initialized
                if (!this.paymentData) {
                    this.paymentData = {};
                }
                if (!this.paymentData.extension_attributes) {
                    this.paymentData.extension_attributes = {};
                }
                
                return this;
            },

            /**
             * Get newsletter subscription value
             */
            getNewsletterSubscribeValue: function() {
                var newsletterCheckbox = $('[name="newsletter_subscribe"]');
                
                if (newsletterCheckbox.length === 0) {
                    newsletterCheckbox = $('#newsletter_subscribe');
                }
                
                if (newsletterCheckbox.length === 0) {
                    newsletterCheckbox = $('.newsletter-subscribe-checkbox');
                }
                
                if (newsletterCheckbox.length === 0) {
                    newsletterCheckbox = $('[data-debug="newsletter-checkbox"]');
                }
                
                if (newsletterCheckbox.length === 0) {
                    $('input[type="checkbox"]').each(function() {
                        var $checkbox = $(this);
                        var $label = $('label[for="' + $checkbox.attr('id') + '"]');
                        if ($label.length > 0 && $label.text().toLowerCase().indexOf('newsletter') !== -1) {
                            newsletterCheckbox = $checkbox;
                            return false;
                        }
                    });
                }
                
                var isChecked = newsletterCheckbox.length > 0 ? newsletterCheckbox.is(':checked') : false;
                
                console.log('Newsletter subscription value during order placement:', isChecked);
                
                return isChecked;
            },

            /**
             * Override placeOrder to handle newsletter subscription
             */
            placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }

                var self = this;

                // Ensure paymentData is properly initialized
                if (!self.paymentData) {
                    self.paymentData = {};
                }
                if (!self.paymentData.extension_attributes) {
                    self.paymentData.extension_attributes = {};
                }

                // Use the proper validation method
                if (this.validate() && 
                    additionalValidators.validate() && 
                    this.isPlaceOrderActionAllowed() === true
                ) {
                    // Get newsletter subscription value before placing order
                    var newsletterSubscribe = this.getNewsletterSubscribeValue();
                    
                    console.log('Placing order with newsletter subscription:', newsletterSubscribe);
                    
                    // Add newsletter subscription to the request data
                    if (newsletterSubscribe) {
                        // Add to the data object that will be sent
                        if (!data) {
                            data = {};
                        }
                        data.newsletter_subscribe = 1;
                        
                        // Also add to payment data
                        self.paymentData.extension_attributes.newsletter_subscribe = 1;
                        console.log('Added newsletter subscription to paymentData.extension_attributes');
                        
                        // Add hidden input to form as backup
                        var hiddenInput = $('<input>').attr({
                            type: 'hidden',
                            name: 'newsletter_subscribe',
                            value: '1'
                        });
                        
                        // Remove any existing hidden input
                        $('input[name="newsletter_subscribe"][type="hidden"]').remove();
                        
                        // Add to form
                        $('form[data-role="checkout-form"]').append(hiddenInput);
                        console.log('Added newsletter subscription to form and data');
                    } else {
                        // Ensure it's set to 0 if not checked
                        self.paymentData.extension_attributes.newsletter_subscribe = 0;
                    }
                    
                    // Call parent method
                    return this._super(data, event);
                }

                return false;
            },

            /**
             * Override getData to include newsletter subscription
             */
            getData: function () {
                var data = this._super();
                
                // Get newsletter subscription value
                var newsletterSubscribe = this.getNewsletterSubscribeValue();
                
                if (newsletterSubscribe) {
                    if (!data.additional_data) {
                        data.additional_data = {};
                    }
                    data.additional_data.newsletter_subscribe = 1;
                    console.log('Added newsletter subscription to getData:', data);
                }
                
                return data;
            }
        });
    };
}); 