<?php 
namespace NeoUnicorn\DigitalGold\Model;

use NeoUnicorn\DigitalGold\Model\ResourceModel\Record\CollectionFactory as RecordCollectionFactory;
use NeoUnicorn\DigitalGold\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Customer\Model\Session;

class GoldDataProvider
{
    protected $recordCollectionFactory;
    protected $transactionCollectionFactory;
    protected $customerSession;

    public function __construct(
        RecordCollectionFactory $recordCollectionFactory,
        TransactionCollectionFactory $transactionCollectionFactory,
        Session $customerSession
    ) {
        $this->recordCollectionFactory = $recordCollectionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->customerSession = $customerSession;
    }

    public function getCustomerGoldData()
    {
        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) {
            return ['record' => [], 'transactions' => []];
        }

        // Fetch Customer Record
        $recordCollection = $this->recordCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->getData();

        // Fetch Customer Transactions
        $transactionCollection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->getData();

        return [
            'record' => $recordCollection,
            'transactions' => $transactionCollection
        ];
    }
}
