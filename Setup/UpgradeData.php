<?php

namespace MageSuite\ProductPositiveIndicators\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $eavSetup;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $moduleDataSetupInterface;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetupInterface,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetupInterface = $moduleDataSetupInterface;
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetupInterface]);
        $this->eavConfig = $eavConfig;

        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetupInterface = $moduleDataSetupInterface;

        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetupInterface]);

        $this->eavConfig = $eavConfig;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->attributeFactory = $attributeFactory;

        $this->connection = $resourceConnection->getConnection();
        $this->logger = $logger;
    }

    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    )
    {
        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->upgradeToVersion002();
        }

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $this->upgradeToVersion003();
        }

        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $this->upgradeToVersion004();
        }

        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $this->upgradeToVersion005();
        }

        if (version_compare($context->getVersion(), '0.0.7', '<')) {
            $this->upgradeToVersion007();
        }

        if (version_compare($context->getVersion(), '0.0.9', '<')) {
            $this->upgradeToVersion009();
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->updateRecentlyAndPopularIndicatorsScope();
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addShippingTimeInDaysAttribute();
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->migrateConfigurationKeys();
        }

    }

    protected function upgradeToVersion002()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'qty_available')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'qty_available',
                [
                    'label' => 'Only X Available Qty',
                    'class' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'group' => 'Positive Indicators',
                    'type' => 'int',
                    'input' => 'text',
                    'frontend' => '',
                    'visible' => 1,
                    'required' => 0,
                    'user_defined' => 1,
                    'used_for_price_rules' => 0,
                    'position' => 1,
                    'unique' => 0,
                    'sort_order' => 10,
                    'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'is_required' => 0,
                    'is_configurable' => 0,
                    'is_searchable' => 0,
                    'is_visible_in_advanced_search' => 0,
                    'is_comparable' => 0,
                    'is_filterable' => 0,
                    'is_filterable_in_search' => 0,
                    'is_used_for_promo_rules' => 0,
                    'is_html_allowed_on_front' => 0,
                    'is_visible_on_front' => 1,
                    'used_in_product_listing' => 1,
                    'used_for_sort_by' => 0,
                    'system' => 0,
                    'note' => 'Display info on PDP if product qty is less than this value. If value is 0 or null, this field will not be used.'
                ]
            );
        }

        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'qty_available')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'qty_available',
                [
                    'type' => 'int',
                    'label' => 'Only X Available Qty',
                    'input' => 'text',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 100,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Positive Indicators',
                    'note' => 'Display info on PDP if product qty is less than this value. If value is 0 or null, this field will not be used. This setting can be overwritten in product.'
                ]
            );
        }
    }

    protected function upgradeToVersion003()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'popular_icon')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'popular_icon',
                [
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'type' => 'int',
                    'unique' => false,
                    'label' => 'Popular Icon',
                    'input' => 'boolean',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'group' => 'Positive Indicators',
                    'required' => false,
                    'sort_order' => 20,
                    'user_defined' => 1,
                    'searchable' => true,
                    'filterable' => true,
                    'filterable_in_search' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'note' => 'Product marked as popular.'
                ]
            );
        }

        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'popular_icon')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'popular_icon',
                [
                    'type' => 'int',
                    'label' => 'Enable Popular Icon',
                    'input' => 'select',
                    'visible' => true,
                    'required' => false,
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'sort_order' => 100,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Positive Indicators',
                    'note' => 'Mark X first products from this category as popular.'
                ]
            );
        }

        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'recently_bought')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'recently_bought',
                [
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'type' => 'int',
                    'unique' => false,
                    'label' => 'Recently Bought Badge',
                    'input' => 'boolean',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'group' => 'Positive Indicators',
                    'required' => false,
                    'sort_order' => 30,
                    'user_defined' => 1,
                    'searchable' => true,
                    'filterable' => true,
                    'filterable_in_search' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'note' => 'Product marked as recently bought.'
                ]
            );
        }

        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'recently_bought_period')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'recently_bought_period',
                [
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'type' => 'int',
                    'unique' => false,
                    'label' => 'Recently Bought: Order Period',
                    'input' => 'text',
                    'group' => 'Positive Indicators',
                    'required' => false,
                    'sort_order' => 40,
                    'user_defined' => 1,
                    'searchable' => true,
                    'filterable' => true,
                    'filterable_in_search' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'note' => 'Specify the time from which orders should be taken, in days. E.g. set 7 if you want use orders from last 7 days.'
                ]
            );
        }

        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'recently_bought_minimal')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'recently_bought_minimal',
                [
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'type' => 'int',
                    'unique' => false,
                    'label' => 'Recently Bought: Minimal Value',
                    'input' => 'text',
                    'group' => 'Positive Indicators',
                    'required' => false,
                    'sort_order' => 50,
                    'user_defined' => 1,
                    'searchable' => true,
                    'filterable' => true,
                    'filterable_in_search' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'note' => 'Specify the minimal amout of purchased products to show Recently Bought badge.'
                ]
            );
        }
    }

    protected function upgradeToVersion004()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'recently_bought_sum')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'recently_bought_sum',
                [
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'type' => 'varchar',
                    'unique' => false,
                    'label' => 'Recently Bought: Orderd sum',
                    'input' => 'text',
                    'group' => 'Positive Indicators',
                    'required' => false,
                    'sort_order' => 60,
                    'user_defined' => 1,
                    'searchable' => true,
                    'filterable' => true,
                    'filterable_in_search' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'note' => 'Sum of ordered products in specific order period.'
                ]
            );
        }
    }

    protected function upgradeToVersion005()
    {
        $attributes = ['popular_icon', 'recently_bought', 'recently_bought_period', 'recently_bought_minimal', 'recently_bought_sum'];
        $entityType = $this->eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

        foreach($attributes as $attribute){
            $this->eavSetup->updateAttribute($entityType, $attribute, 'is_searchable', false);
            $this->eavSetup->updateAttribute($entityType, $attribute, 'is_filterable', false);
        }
    }

    protected function upgradeToVersion007()
    {
        $entityType = $this->eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

        $this->eavSetup->updateAttribute($entityType, 'recently_bought_sum', 'frontend_label', 'Recently Bought: Ordered sum');
        $this->eavSetup->updateAttribute($entityType, 'recently_bought_sum', 'note', 'Sum of orders in specific order period.');
        $this->eavSetup->updateAttribute($entityType, 'recently_bought_minimal', 'note', 'Specify the minimal amount of orders to show Recently Bought badge.');
    }

    protected function upgradeToVersion009()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'popular_icon_categories')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'popular_icon_categories',
                [
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'type' => 'varchar',
                    'unique' => false,
                    'label' => 'Popular Icon Categories',
                    'input' => 'text',
                    'group' => 'Positive Indicators',
                    'required' => false,
                    'sort_order' => 25,
                    'user_defined' => 1,
                    'searchable' => true,
                    'filterable' => true,
                    'filterable_in_search' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'note' => 'Categories IDs in which product is marked as popular. Updated by cron every night.'
                ]
            );
        }

        $entityType = $this->eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $this->eavSetup->updateAttribute($entityType, 'popular_icon', 'used_in_product_listing', true);
    }

    public function updateRecentlyAndPopularIndicatorsScope()
    {
        $entityType = $this->eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributes = [
            'recently_bought',
            'recently_bought_period',
            'recently_bought_minimal',
            'recently_bought_sum',
            'popular_icon',
            'popular_icon_categories'
        ];

        foreach($attributes as $attribute){
            $this->eavSetup->updateAttribute($entityType, $attribute, 'is_global', \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL);
        }
    }

    public function addShippingTimeInDaysAttribute()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'use_time_needed_to_ship_product')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'use_time_needed_to_ship_product',
                [
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'type' => 'int',
                    'unique' => false,
                    'label' => 'Use specific Shipping time',
                    'input' => 'boolean',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'group' => 'Positive Indicators',
                    'required' => false,
                    'sort_order' => 70,
                    'user_defined' => 1,
                    'searchable' => false,
                    'filterable' => false,
                    'filterable_in_search' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'note' => 'Take into account specific time needed to ship product'
                ]
            );
        }

        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'time_needed_to_ship_product')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'time_needed_to_ship_product',
                [
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'type' => 'varchar',
                    'unique' => false,
                    'label' => 'Specific shipping time',
                    'input' => 'text',
                    'group' => 'Positive Indicators',
                    'required' => false,
                    'sort_order' => 80,
                    'user_defined' => 1,
                    'searchable' => false,
                    'filterable' => false,
                    'filterable_in_search' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'note' => 'Specific time needed to ship product'
                ]
            );
        }
    }

    protected function migrateConfigurationKeys()
    {
        $table = $this->connection->getTableName('core_config_data');
        $format = 'positive_indicators/%s/%s';

        $indicators = ['only_x_available', 'popular_icon', 'recently_bought', 'fast_shipping', 'expected_delivery'];

        try{
            foreach($indicators as $indicator){
                $this->connection->update(
                    $table,
                    ['path' => sprintf($format, $indicator, 'is_enabled')],
                    ['path = ?' => sprintf($format, $indicator, 'active')]
                );
            }
        }catch (\Exception $e){
            $message = sprintf('Error during ProductPositiveIndicators\Setup\UpgradeData::migrateConfigurationKeys(): %s', $e->getMessage());
            $this->logger->warning($message);
        }
    }

}