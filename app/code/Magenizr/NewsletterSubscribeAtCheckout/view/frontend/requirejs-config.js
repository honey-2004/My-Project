/**
 * Magenizr NewsletterSubscriptionAtCheckout
 *
 * @category    Magenizr
 * @package     Magenizr_NewsletterSubscriptionAtCheckout
 * @copyright   Copyright (c) 2018 Magenizr (http://www.magenizr.com)
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/payment/default': {
                'Magenizr_NewsletterSubscribeAtCheckout/js/view/payment/default-mixin': true
            }
        }
    }
};