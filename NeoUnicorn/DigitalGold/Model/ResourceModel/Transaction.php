<?php
namespace NeoUnicorn\DigitalGold\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Transaction extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('digital_gold_transactions', 'entity_id');
    }
}