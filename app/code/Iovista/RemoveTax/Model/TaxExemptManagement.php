<?php
namespace Iovista\RemoveTax\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\ValidatorException;

class TaxExemptManagement implements \Iovista\RemoveTax\Api\TaxExemptManagementInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     * @param \Psr\Log\LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param int $cartId
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function removeTax($cartId)
    {
        $result = false;
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $quote->setCustomerTaxClassId(0); // Remove customer tax class
            foreach ($quote->getAllItems() as $quoteItem) {
                $quoteItem->setTaxAmount(0);
                $quoteItem->setBaseTaxAmount(0);
                $quoteItem->setTaxPercent(0);
                $quoteItem->setPriceInclTax($quoteItem->getPrice());
                $quoteItem->setBasePriceInclTax($quoteItem->getBasePrice());
                $quoteItem->setRowTotalInclTax($quoteItem->getRowTotal());
                $quoteItem->setBaseRowTotalInclTax($quoteItem->getBaseRowTotal());
                $quoteItem->save();
            }

            $subtotal = $quote->getShippingAddress()->getSubtotal();
            $shippingAmount = $quote->getShippingAddress()->getShippingAmount();

            $finalGrandTotal = floatval($subtotal) + floatval($shippingAmount);
            
            // Remove tax amount and update cart total
            $quote->getShippingAddress()->setTaxAmount(0);
            $quote->getShippingAddress()->setBaseTaxAmount(0);
            $quote->getShippingAddress()->setGrandTotal($finalGrandTotal);
            $quote->getShippingAddress()->setBaseGrandTotal($finalGrandTotal);
            $quote->setSubtotalInclTax($quote->getSubtotal());
            $quote->setBaseSubtotalInclTax($quote->getBaseSubtotal());
            $quote->setGrandTotal($finalGrandTotal);
            $quote->setBaseGrandTotal($finalGrandTotal);

            // Set 'state_tax_flag' to '1' to update the order total amounts after place order
            $quote->setData('state_tax_flag', 1);

            $quote->save();
            $quote->collectTotals(); // Recalculate totals

            $result = true;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('The tax relief could not be saved'));
        }

        return $result;
    }

    /**
     * @param int $cartId
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function resetStateFlag($cartId)
    {
        $result = false;
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $quote->collectTotals();

            $quote->setCustomerTaxClassId($quote->getCustomerTaxClassId()); // Add customer tax class

            foreach ($quote->getAllItems() as $quoteItem) {
                $quoteItem->setTaxAmount($quoteItem->getTaxAmount());
                $quoteItem->setBaseTaxAmount($quoteItem->getBaseTaxAmount());
                $quoteItem->setTaxPercent($quoteItem->getTaxPercent());
                $quoteItem->setPriceInclTax($quoteItem->getPriceInclTax());
                $quoteItem->setBasePriceInclTax($quoteItem->getBasePriceInclTax());
                $quoteItem->setRowTotalInclTax($quoteItem->getRowTotalInclTax());
                $quoteItem->setBaseRowTotalInclTax($quoteItem->getBaseRowTotalInclTax());
                $quoteItem->save();
            }

            $subtotal = $quote->getShippingAddress()->getSubtotal();
            $shippingAmount = $quote->getShippingAddress()->getShippingAmount();
            
            // Reassign the tax amount and update cart total
            $quote->getShippingAddress()->setTaxAmount($quote->getShippingAddress()->getTaxAmount());
            $quote->getShippingAddress()->setBaseTaxAmount($quote->getShippingAddress()->getBaseTaxAmount());
            $quote->getShippingAddress()->setGrandTotal($quote->getShippingAddress()->getGrandTotal());
            $quote->getShippingAddress()->setBaseGrandTotal($quote->getShippingAddress()->getBaseGrandTotal());
            $quote->setSubtotalInclTax($quote->getSubtotal());
            $quote->setBaseSubtotalInclTax($quote->getBaseSubtotal());
            $quote->setGrandTotal($quote->getGrandTotal());
            $quote->setBaseGrandTotal($quote->getBaseGrandTotal());

            // Set 'state_tax_flag' to '0' to restrict the order total amounts after place order
            $quote->setData('state_tax_flag', 0);
            
            $quote->save();
            $quote->collectTotals(); // Recalculate totals
            
            $result = true;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('The tax relief could not be saved'));
        }

        return $result;
    }
}
