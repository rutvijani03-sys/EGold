<?php

namespace NeoUnicorn\DigitalGold\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use NeoUnicorn\DigitalGold\Model\RecordFactory;
use NeoUnicorn\DigitalGold\Model\TransactionFactory;


class AfterInvoicePay implements ObserverInterface
{
    protected $transactionFactory;
    protected $recordFactory;

    public function __construct(
        TransactionFactory $transactionFactory,
        RecordFactory $recordFactory
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->recordFactory = $recordFactory;
    }

    public function execute(Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $orderId = $order->getIncrementId();

        $transaction = $this->transactionFactory->create();
        $transaction->load($orderId, 'order_id');
        if ($transaction->getId()) {
            $transaction->setStatus('Approved');
            $transaction->save();
            $record = $this->recordFactory->create();
            $record->load($transaction->getCustomerId(), 'customer_id');
            if ($record->getId()) {
                $record->setTotalGold($record->getTotalGold() + $transaction->getQuantity());
                $record->setRemainingGold($record->getRemainingGold() +  $transaction->getQuantity());
            } else {
                $record->setData([
                    'customer_id'    => $transaction->getCustomerId(),
                    'total_gold'     => $transaction->getQuantity(),
                    'used_gold'      => 0,
                    'remaining_gold' => $transaction->getQuantity(),
                    'product_id'     => $transaction->getProductId()
                ]);
            }
            $record->save();
        }
    }
}
