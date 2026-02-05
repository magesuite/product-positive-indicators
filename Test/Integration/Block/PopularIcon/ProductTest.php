<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Block\PopularIcon;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ProductTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Framework\Registry $coreRegistry;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected ?\MageSuite\ProductPositiveIndicators\Block\PopularIcon\Product $productBlock;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->coreRegistry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->productBlock = $this->objectManager->get(\MageSuite\ProductPositiveIndicators\Block\PopularIcon\Product::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     */
    public function testItReturnCorrectFlag(string $sku, bool $flag): void
    {
        $product = $this->productRepository->get($sku);
        $this->coreRegistry->register('product', $product);

        $popularIconFlag = $this->productBlock->getPopularIconFlag();

        $this->assertEquals($flag, $popularIconFlag);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     */
    public function testItReturnsFalseWhenNoCurrentProductIsRegistered(): void
    {
        $this->coreRegistry->register('product', null);
        $popularIconFlag = $this->productBlock->getPopularIconFlag();

        $this->assertNull($popularIconFlag);
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
