<?php

namespace NeoUnicorn\DigitalGold\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\Product;

class CreateGoldPriceAttributes implements DataPatchInterface
{
    private $moduleDataSetup;
    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributeCode = 'gold_price';

        // Add the attribute
        $eavSetup->addAttribute(
            Product::ENTITY,
            $attributeCode,
            [
                'type' => 'decimal',
                'label' => 'Gold Price',
                'input' => 'price',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'used_in_product_listing' => true,
                'user_defined' => true,
                'backend' => \Magento\Catalog\Model\Product\Attribute\Backend\Price::class,
                'group' => 'Jewellery Details', // Optional but useful
            ]
        );

        // Assign the attribute to Default Attribute Set & Group
        $defaultAttributeSetId = $eavSetup->getDefaultAttributeSetId(Product::ENTITY);
        $attributeGroupName = 'Jewellery Details';
        $attributeGroupId = $eavSetup->getAttributeGroupId(Product::ENTITY, $defaultAttributeSetId, $attributeGroupName);

        if (!$attributeGroupId) {
            // If the group doesn't exist, create it
            $attributeGroupId = $eavSetup->addAttributeGroup(
                Product::ENTITY,
                $defaultAttributeSetId,
                $attributeGroupName,
                100 // sort order
            );
        }

        // Assign attribute to the group
        $eavSetup->addAttributeToGroup(
            Product::ENTITY,
            $defaultAttributeSetId,
            $attributeGroupId,
            $attributeCode,
            50 // sort order within group
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
