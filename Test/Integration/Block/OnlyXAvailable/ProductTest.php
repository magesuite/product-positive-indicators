<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Block\OnlyXAvailable;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ProductTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Framework\Registry $coreRegistry;
    protected ?\Magento\CatalogInventory\Api\StockStateInterface $stockInterface;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected ?\MageSuite\ProductPositiveIndicators\Block\OnlyXAvailable\Product $productBlock;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->coreRegistry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->stockInterface = $this->objectManager->get(\Magento\CatalogInventory\Api\StockStateInterface::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->productBlock = $this->objectManager->get(\MageSuite\ProductPositiveIndicators\Block\OnlyXAvailable\Product::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/only_x_available/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/only_x_available/quantity 10
     */
    public function testItReturnsCorrectFlag(string $sku, bool $flag): void
    {
        $product = $this->productRepository->get($sku);
        $this->coreRegistry->register('product', $product);

        $displayInfo = $this->productBlock->shouldDisplayInfoOnProductPage();

        $this->assertEquals($flag, $displayInfo);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/only_x_available/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/only_x_available/quantity 10
     */
    public function testItReturnsCorrectFlagForQtyParameter(string $sku, bool $flag): void
    {
        $product = $this->productRepository->get($sku);
        $productQty = $this->stockInterface->getStockQty($product->getId());

        $displayInfo = $this->productBlock->shouldDisplayInfoOnProductPage($productQty);

        $this->assertEquals($flag, $displayInfo);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @magentoConfigFixture current_store positive_indicators/only_x_available/is_enabled 1
     */
    public function testItReturnsFalseIfConfigurationIsNotSet(): void
    {
        $product = $this->productRepository->get('product_qty_100');
        $this->coreRegistry->register('product', $product);

        $displayInfo = $this->productBlock->shouldDisplayInfoOnProductPage();

        $this->assertFalse($displayInfo);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store positive_indicators/only_x_available/is_enabled 1
     */
    public function testItReturnsFalseWhenNoCurrentProductIsRegistered(): void
    {
        $this->coreRegistry->register('product', null);

        $displayInfo = $this->productBlock->shouldDisplayInfoOnProductPage();

        $this->assertFalse($displayInfo);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @magentoConfigFixture current_store positive_indicators/only_x_available/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/only_x_available/quantity 10
     */
    public function testItReturnsCorrectFlagForQtyParameterFromProduct(): void
    {
        $product = $this->productRepository->get('product_qty_available');
        $this->coreRegistry->register('product', $product);

        $displayInfo = $this->productBlock->shouldDisplayInfoOnProductPage();

        $this->assertTrue($displayInfo);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @magentoConfigFixture current_store positive_indicators/only_x_available/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/only_x_available/quantity 5
     */
    public function testItReturnsFalseWhenBackordersEnabled(): void
    {
        $product = $this->productRepository->get('product_backorders_enabled');
        $this->coreRegistry->register('product', $product);

        $displayInfo = $this->productBlock->shouldDisplayInfoOnProductPage();

        $this->assertFalse($displayInfo);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @magentoConfigFixture current_store positive_indicators/only_x_available/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/only_x_available/quantity 10
     */
    public function testItReturnsCorrectProductQty(): void
    {
        $product = $this->productRepository->get('product_qty_100');
        $this->coreRegistry->register('product', $product);

        $this->assertEquals(100, $this->productBlock->getProductQty());

        $product = $this->productRepository->get('product_qty_2');

        $this->coreRegistry->unregister('product');
        $this->coreRegistry->register('product', $product);

        $this->assertEquals(2, $this->productBlock->getProductQty());
    }

    public static function getExpectedData(): array
    {
        return [
            ['product_qty_100', false],
            ['product_qty_2', true],
            ['product_qty_0', false],
        ];
    }
}
