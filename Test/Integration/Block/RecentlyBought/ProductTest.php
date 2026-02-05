<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Block\RecentlyBought;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ProductTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Framework\Registry $coreRegistry;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected ?\MageSuite\ProductPositiveIndicators\Block\RecentlyBought\Product $productBlock;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->coreRegistry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->productBlock = $this->objectManager->get(\MageSuite\ProductPositiveIndicators\Block\RecentlyBought\Product::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/recently_bought/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/recently_bought/period 7
     */
    public function testItReturnCorrectFlag(string $sku, array $data): void
    {
        $product = $this->productRepository->get($sku);
        $this->coreRegistry->register('product', $product);

        $recentlyBoughtInfo = $this->productBlock->getRecentlyBoughtInfo();

        $this->assertEquals($data, $recentlyBoughtInfo);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store positive_indicators/recently_bought/is_enabled 1
     */
    public function testItReturnsFalseWhenNoCurrentProductIsRegistered(): void
    {
        $this->coreRegistry->register('product', null);

        $recentlyBoughtInfo = $this->productBlock->getRecentlyBoughtInfo();

        $this->assertEquals(['active' => 0], $recentlyBoughtInfo);
    }

    public static function getExpectedData(): array
    {
        return [
            ['product_qty_100', ['active' => 0]],
            ['product_qty_2', ['active' => 1, 'sum' => 100, 'order_period' => 7]],
            ['product_qty_0', ['active' => 0]],
            ['product_qty_available', ['active' => 1, 'sum' => 100, 'order_period' => 14]],
            ['additional_product', ['active' => 0]]
        ];
    }
}
