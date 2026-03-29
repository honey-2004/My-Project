<?php
namespace Iovista\RemoveTax\Api;

/**
 * Interface to manage the tax amount in cart & order total for guest user.
 */
interface GuestTaxExemptManagementInterface
{
    /**
     * @param string $cartId
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function removeTax($cartId);

    /**
     * @param string $cartId
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function resetStateFlag($cartId);
}
