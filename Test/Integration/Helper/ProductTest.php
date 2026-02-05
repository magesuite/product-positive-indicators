<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Helper;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ProductTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\MageSuite\ProductPositiveIndicators\Helper\Product $productHelper;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productHelper = $this->objectManager->get(\MageSuite\ProductPositiveIndicators\Helper\Product::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     */
    public function testItReturnsCorrectFlag(int $productId, bool $flag): void
    {
        $popularIconFlag = $this->productHelper->getPopularIconFlag($productId);

        $this->assertEquals($flag, $popularIconFlag);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/products.php
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 0
     */
    public function testItReturnsFalseIfConfigurationIsNotSet(): void
    {
        $popularIconFlag = $this->productHelper->getPopularIconFlag(601);

        $this->assertFalse($popularIconFlag);
    }

    public static function getExpectedData(): array
    {
        return [
            [600, false],
            [601, true],
            [602, false],
        ];
    }
}
