<?php

namespace MageSuite\ProductPositiveIndicators\Setup\Patch\Data;

class RemoveStoreAttributeValues implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();
        $attributeCodes = [
            'popular_icon',
            'popular_icon_categories',
            'qty_available',
            'recently_bought',
            'recently_bought_period',
            'recently_bought_minimal',
            'recently_bought_sum',
            'use_time_needed_to_ship_product'
        ];
        $collection = $this->attributeCollectionFactory->create();
        $collection->addFieldToFilter('attribute_code', ['in' => $attributeCodes])
            ->addFieldToFilter('is_global', 1);

        foreach ($collection as $attribute) {
            $where = [
                'attribute_id = ?' => $attribute->getId(),
                'store_id > ?' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ];
            $connection->delete($attribute->getBackendTable(), $where);
        }

        $connection->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
