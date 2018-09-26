<?php

namespace MageSuite\ProductPositiveIndicators\Setup;


class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.0.6', '<')) {
            $this->upgradeToVersion006($setup);
        }
    }

    public function upgradeToVersion006(\Magento\Framework\Setup\SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        $table = $setup->getTable('catalog_eav_attribute');
        $connection = $setup->getConnection();
        if(!$connection->tableColumnExists($table, 'top_attribute_enabled')) {
            $connection->addColumn(
                $table,
                'top_attribute_enabled',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'comment' => 'Field with options disable/enable indicator.',
                    'nullable' => true
                ]
            );
        }
        if(!$connection->tableColumnExists($table, 'top_attribute_sign')) {
            $connection->addColumn(
                $table,
                'top_attribute_sign',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Text field, sign from this field will be used as parser instruction.',
                    'nullable' => true
                ]
            );
        }
        if(!$connection->tableColumnExists($table, 'top_attribute_value')) {
            $connection->addColumn(
                $table,
                'top_attribute_value',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Text field, value from this field will be used as parser instruction.',
                    'nullable' => true
                ]
            );
        }
        if(!$connection->tableColumnExists($table, 'top_attribute_min_value')) {
            $connection->addColumn(
                $table,
                'top_attribute_min_value',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'This field is calculated automatically in case of % sign chosen and stores minimal value to show an indicator.',
                    'nullable' => true
                ]
            );
        }

        $setup->endSetup();
    }
}