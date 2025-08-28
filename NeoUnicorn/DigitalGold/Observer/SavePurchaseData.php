<?php
namespace NeoUnicorn\DigitalGold\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use NeoUnicorn\DigitalGold\Model\RecordFactory;

use NeoUnicorn\DigitalGold\Model\TransactionFactory;

class SavePurchaseData implements ObserverInterface
{
    protected $recordFactory;
    protected $transactionFactory;

    public function __construct(
        RecordFactory $recordFactory,
        TransactionFactory $transactionFactory
    ) {
        $this->recordFactory = $recordFactory;
        $this->transactionFactory = $transactionFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        // Check if the order contains the virtual product (digital gold)
        $isDigitalGoldOrder = false;
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() === 'virtual' && $product->getSku() == 'digital_gold') {
                $isDigitalGoldOrder = true;
                $customerId = $order->getCustomerId();
                $orderId = $order->getIncrementId();
                $qty = (10*$item->getRowTotal())/$item->getProduct()->getGoldPrice();
                $amount = $item->getRowTotal();
                $productId = $item->getProductId();
                $transactionnote = "Order Id ".$order->getIncrementId()." credited Gold";
                $action = "credit";
                $status ="Pending";
                break;
            }
        }

        if (!$isDigitalGoldOrder) {
            return;
        }


        // Save data to the purchase table
        // $record = $this->recordFactory->create();
        // $record->load($customerId, 'customer_id');
        // if($record->getId()){
        //     $record->setTotalGold($record->getTotalGold() + $qty);
        //     $record->setRemainingGold($record->getRemainingGold() + $qty);
        // }
        // else{
        //     $record->setData([
        //         'customer_id'    => $customerId,
        //         'total_gold'     => $qty,
        //         'used_gold'      => 0,
        //         'remaining_gold' => $qty,
        //         'product_id'     => $productId
        //     ]);
        // }
        // $record->save();

        // Save data to the transaction table
        $transaction = $this->transactionFactory->create();
        $transaction
            ->setCustomerId($customerId)
            ->setProductId($productId)
            ->setAmount($amount)
            ->setQuantity($qty)
            ->setOrderId($orderId)
            ->setTransactionNote($transactionnote)
            ->setAction($action)
            ->setStatus($status)
            ->save();
    }
}
