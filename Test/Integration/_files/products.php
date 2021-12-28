<?php
use Magento\CatalogInventory\Model\Stock;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(600)
    ->setAttributeSetId(4)
    ->setName('Product with qty 100')
    ->setSku('product_qty_100')
    ->setUrlKey('product_qty_100')
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
    ->setName('Product with qty 2')
    ->setSku('product_qty_2')
    ->setUrlKey('product_qty_2')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 2, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setPopularIcon(1)
    ->setRecentlyBought(1)
    ->setRecentlyBoughtSum(100)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(602)
    ->setAttributeSetId(4)
    ->setName('Product with qty 0')
    ->setSku('product_qty_0')
    ->setUrlKey('product_qty_0')
    ->setPrice(5)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 0, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setPopularIcon(0)
    ->setRecentlyBought(0)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(603)
    ->setAttributeSetId(4)
    ->setName('Product with available qty')
    ->setSku('product_qty_available')
    ->setUrlKey('product_qty_available')
    ->setPrice(40)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 40, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setQtyAvailable(50)
    ->setRecentlyBought(1)
    ->setRecentlyBoughtSum(100)
    ->setRecentlyBoughtPeriod(14)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(604)
    ->setAttributeSetId(4)
    ->setName('Additional product')
    ->setSku('additional_product')
    ->setUrlKey('additional_product')
    ->setPrice(50)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setRecentlyBought(1)
    ->setRecentlyBoughtSum(0)
    ->setRecentlyBoughtPeriod(7)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(605)
    ->setAttributeSetId(4)
    ->setName('Product with backorders')
    ->setSku('product_backorders_enabled')
    ->setUrlKey('product_backorders_enabled')
    ->setPrice(50)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1, 'backorders' => Stock::BACKORDERS_YES_NOTIFY])
    ->setCanSaveCustomOptions(true)
    ->setRecentlyBought(1)
    ->setRecentlyBoughtSum(0)
    ->setRecentlyBoughtPeriod(7)
    ->save();

$product->reindex();
$product->priceReindexCallback();