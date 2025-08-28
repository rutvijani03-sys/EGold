<?php
    namespace NeoUnicorn\DigitalGold\Observer;

    use Magento\Framework\Event\ObserverInterface;
    use Magento\Framework\App\RequestInterface;
    use Magento\Catalog\Model\Product\Type;

    class CustomPrice implements ObserverInterface
    {
        public function execute(\Magento\Framework\Event\Observer $observer) {
            $item = $observer->getEvent()->getData('quote_item');
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );


        $product = $item->getProduct();

        // Only apply to virtual products (optional)
        if ($product->getTypeId() !== Type::TYPE_VIRTUAL && $product->getSku() !== 'digital_gold') {
            return;
        }
        if($product->getTypeId() == Type::TYPE_VIRTUAL && $product->getSku() !== 'digital_gold'){
            return;
        }
        if($product->getSku() == 'wk_wallet_amount' ){
            return;
        }
            $options = $item->getOptions();
            foreach ($options as $option) {
                if (strpos($option->getCode(), 'option_') !== false) {
                    $customValue = $option->getValue();
                }

            }

            $price = $customValue; //set your price here
                $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        }

    }
