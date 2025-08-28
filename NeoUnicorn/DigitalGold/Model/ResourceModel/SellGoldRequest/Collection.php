<?php 
namespace NeoUnicorn\DigitalGold\Model\ResourceModel\SellGoldRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use NeoUnicorn\DigitalGold\Model\SellGoldRequest;
use NeoUnicorn\DigitalGold\Model\ResourceModel\SellGoldRequest as SellGoldRequestResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(SellGoldRequest::class, SellGoldRequestResource::class);
    }
}
