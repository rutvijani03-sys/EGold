<?php
namespace NeoUnicorn\DigitalGold\Model;

use Magento\Framework\Model\AbstractModel;

class Record extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\NeoUnicorn\DigitalGold\Model\ResourceModel\Record::class);
    }
}