<?php
namespace NeoUnicorn\DigitalGold\Model;

use Magento\Framework\Model\AbstractModel;

class SellGoldRequest extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\NeoUnicorn\DigitalGold\Model\ResourceModel\SellGoldRequest::class);
    }
}
