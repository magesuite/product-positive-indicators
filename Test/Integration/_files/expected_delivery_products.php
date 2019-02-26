<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(605)
    ->setAttributeSetId(4)
    ->setName('Simple product')
    ->setSku('simple_product')
    ->setUrlKey('simple_product')
    ->setPrice(50)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(606)
    ->setAttributeSetId(4)
    ->setName('Out of stock product')
    ->setSku('out_of_stock')
    ->setUrlKey('out_of_stock')
    ->setPrice(50)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 0])
    ->setCanSaveCustomOptions(true)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(607)
    ->setAttributeSetId(4)
    ->setName('Custom product')
    ->setSku('custom_product')
    ->setUrlKey('custom_product')
    ->setPrice(50)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setUseTimeNeededToShipProduct(1)
    ->setTimeNeededToShipProduct(4)
    ->save();

$product->reindex();
$product->priceReindexCallback();