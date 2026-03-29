<?php
namespace Iovista\RemoveTax\Api;

/**
 * Interface to manage the tax amount in cart & order total for logged in user.
 * @api
 */
interface TaxExemptManagementInterface
{
    /**
     * @param int $cartId
     * @return string
     */
    public function removeTax($cartId);

    /**
     * @param int $cartId
     * @return string
     */
    public function resetStateFlag($cartId);
}
