<?php 
namespace NeoUnicorn\DigitalGold\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SellGoldRequest extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('digital_gold_sell_request', 'entity_id');
    }
}
