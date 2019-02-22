<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(600)
    ->setAttributeSetId(4)
    ->setName('Product with price 10')
    ->setSku('product_price_10')
    ->setUrlKey('product_price_10')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(601)
    ->setAttributeSetId(4)
    ->setName('Product with price 5')
    ->setSku('product_price_5')
    ->setUrlKey('product_price_5')
    ->setPrice(5)
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
    ->setId(602)
    ->setAttributeSetId(4)
    ->setName('Product with price 20')
    ->setSku('product_price_20')
    ->setUrlKey('product_price_20')
    ->setPrice(20)
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
    ->setId(603)
    ->setAttributeSetId(4)
    ->setName('Product with price 40')
    ->setSku('product_price_40')
    ->setUrlKey('product_price_40')
    ->setPrice(40)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(604)
    ->setAttributeSetId(4)
    ->setName('Product with price 50')
    ->setSku('product_price_50')
    ->setUrlKey('product_price_50')
    ->setPrice(50)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setRecentlyBoughtPeriod(50)
    ->setRecentlyBoughtMinimal(10)
    ->save();

$product->reindex();
$product->priceReindexCallback();

/** @var \Magento\Catalog\Model\Category $category */
$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->isObjectNew(true);
$category
    ->setId(433)
    ->setCreatedAt('2014-06-23 09:50:07')
    ->setName('First category')
    ->setParentId(2)
    ->setPath('1/2/333')
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->setAvailableSortBy(['position'])
    ->setPostedProducts([
        600 => 10,
        601 => 11,
        602 => 12,
        603 => 13,
        604 => 14
    ])
    ->save()
    ->reindex();

/** @var \Magento\Catalog\Model\Category $category */
$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->isObjectNew(true);
$category
    ->setId(434)
    ->setCreatedAt('2014-06-23 09:50:07')
    ->setName('Subcategory')
    ->setParentId(2)
    ->setPath('1/2/333/334')
    ->setLevel(4)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->setAvailableSortBy(['position'])
    ->setPostedProducts([
        601 => 11
    ])
    ->save()
    ->reindex();
