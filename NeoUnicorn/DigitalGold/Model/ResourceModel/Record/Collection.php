<?php
namespace NeoUnicorn\DigitalGold\Model\ResourceModel\Record;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \NeoUnicorn\DigitalGold\Model\Record::class,
            \NeoUnicorn\DigitalGold\Model\ResourceModel\Record::class
        );
    }
}