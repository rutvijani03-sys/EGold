<?php 
namespace NeoUnicorn\DigitalGold\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use NeoUnicorn\DigitalGold\Model\Transaction;
use NeoUnicorn\DigitalGold\Model\ResourceModel\Transaction as TransactionResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Transaction::class, TransactionResource::class);
    }
}
