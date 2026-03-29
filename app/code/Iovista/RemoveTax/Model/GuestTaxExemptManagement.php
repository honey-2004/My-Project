<?php
namespace Iovista\RemoveTax\Model;

use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestTaxExemptManagement implements \Iovista\RemoveTax\Api\GuestTaxExemptManagementInterface
{

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Iovista\RemoveTax\Api\TaxExemptManagementInterface
     */
    protected $taxExemptManagement;
    
    /**
     * GuestTaxExemptManagement constructor.
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Iovista\RemoveTax\Api\TaxExemptManagementInterface $taxExemptManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        \Iovista\RemoveTax\Api\TaxExemptManagementInterface $taxExemptManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->taxExemptManagement = $taxExemptManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function removeTax($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->taxExemptManagement->removeTax($quoteIdMask->getQuoteId());
    }

    /**
     * {@inheritDoc}
     */
    public function resetStateFlag($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->taxExemptManagement->resetStateFlag($quoteIdMask->getQuoteId());
    }
}
