<?php
namespace NeoUnicorn\DigitalGold\Model;

use Magento\Framework\Model\AbstractModel;

class Transaction extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\NeoUnicorn\DigitalGold\Model\ResourceModel\Transaction::class);
    }
}