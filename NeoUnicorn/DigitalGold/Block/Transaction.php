<?php 
namespace NeoUnicorn\DigitalGold\Block;

use Magento\Framework\View\Element\Template;
use NeoUnicorn\DigitalGold\Model\GoldDataProvider;

class Transaction extends Template
{
    protected $goldDataProvider;

    public function __construct(
        Template\Context $context,
        GoldDataProvider $goldDataProvider,
        array $data = []
    ) {
        $this->goldDataProvider = $goldDataProvider;
        parent::__construct($context, $data);
    }

    public function getGoldData()
    {
        return $this->goldDataProvider->getCustomerGoldData();
    }
}