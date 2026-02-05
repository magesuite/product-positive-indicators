<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Model;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class RecentlyBoughtProductsTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected ?\MageSuite\ProductPositiveIndicators\Model\RecentlyBoughtProducts $recentlyBoughtProducts;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->recentlyBoughtProducts = $this->objectManager->get(\MageSuite\ProductPositiveIndicators\Model\RecentlyBoughtProducts::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/orders.php
     * @magentoConfigFixture current_store positive_indicators/recently_bought/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/recently_bought/period 7
     * @magentoConfigFixture current_store positive_indicators/recently_bought/minimal 2
     */
    public function testItSetCorrectFlagInProducts(): void
    {
        $this->recentlyBoughtProducts->execute();

        $productNotBought = $this->productRepository->get('product_qty_available');
        $this->assertNotTrue($productNotBought->getRecentlyBought());

        $productBought = $this->productRepository->get('product_qty_2');
        $this->assertEquals(1, $productBought->getRecentlyBought());
        $this->assertEquals(3, $productBought->getRecentlyBoughtSum());
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/orders.php
     * @magentoConfigFixture current_store positive_indicators/recently_bought/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/recently_bought/period 7
     * @magentoConfigFixture current_store positive_indicators/recently_bought/minimal 2
     */
    public function testItReturnsCorrectProductsDataForSpecificSettings(): void
    {
        $expectedResult = [
            601 => 3,
            602 => 2,
            603 => 2,
            608 => 2
        ];

        $productIds = $this->recentlyBoughtProducts->getProductsData();

        $this->assertEquals($expectedResult, $productIds);
    }
}
