<?php
namespace Iovista\RemoveTax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class AddTotalToOrderObserver implements ObserverInterface
{
    protected $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/remove_tax.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();

        $incrementId = $order->getIncrementId();

        $logger->info('------- AddTotalToOrderObserver Process Start for Order ID: '. $incrementId .' -------');

        try {
            $stateTaxFlag = $quote->getData('state_tax_flag');

            if($stateTaxFlag == 1) {

                $logger->info('The order is from Indiana state & selected as the tax exempt state...!!!');

                $logger->info('The tax exempt number is: '.$quote->getPayment()->getData('tax_relief_code'));
                $logger->info('The tax exempt state is: '.$quote->getPayment()->getData('tax_relief_state'));

                // Updating the order item's value to remove tax amount
                foreach ($quote->getAllItems() as $quoteItem) {
                    // Find the corresponding order item
                    $orderItem = $order->getItemByQuoteItemId($quoteItem->getItemId());

                    if ($orderItem) {
                        $orderItem->setOriginalPrice($quoteItem->getOriginalPrice());
                        $orderItem->setPrice($quoteItem->getPrice());
                        $orderItem->setRowTotal($quoteItem->getRowTotal());
                        $orderItem->setBaseRowTotal($quoteItem->getBaseRowTotal());
                        $orderItem->setTaxPercent(0);
                        $orderItem->setTaxAmount(0);
                        $orderItem->setBaseTaxAmount(0);
                        $orderItem->setPriceInclTax($quoteItem->getPrice());
                        $orderItem->setBasePriceInclTax($quoteItem->getPrice());
                        $orderItem->setRowTotalInclTax($quoteItem->getRowTotal());
                        $orderItem->setBaseRowTotalInclTax($quoteItem->getRowTotal());
                    }
                }

                // Updating order total to remove tax amount if Indiana state is in customer address.

                // Get cart total amount from the quote
                $grandTotal = $quote->getGrandTotal();
                $subtotal = $quote->getSubtotal();
                $taxAmount = $quote->getShippingAddress()->getTaxAmount();

                $logger->info('The order total before remove tax is: '.$order->getGrandTotal());

                $finalGrandTotal = $grandTotal - $taxAmount;

                $order->setGrandTotal($finalGrandTotal);
                $order->setBaseGrandTotal($finalGrandTotal);
                $order->setSubtotal($subtotal);
                $order->setBaseSubtotal($subtotal);
                $order->setTaxAmount(0);
                $order->setBaseTaxAmount(0);

                $order->save();

                $logger->info('The order total after remove tax is: '.$order->getGrandTotal());
            }else{
                $logger->info('The order is not from Indiana state...!!!');
            }
        } catch (\Exception $e) {
            $logger->info($e->getMessage());
        }

        $logger->info('------- AddTotalToOrderObserver Process End -------');
        return $this;
    }
}
