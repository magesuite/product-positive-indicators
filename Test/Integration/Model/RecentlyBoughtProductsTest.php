<?php

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Model;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class RecentlyBoughtProductsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Model\RecentlyBoughtProducts
     */
    protected $recentlyBoughtProducts;

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
     * @magentoDataFixture loadOrders
     * @magentoConfigFixture current_store positive_indicators/recently_bought/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/recently_bought/period 7
     * @magentoConfigFixture current_store positive_indicators/recently_bought/minimal 2
     */
    public function testItSetCorrectFlagInProducts()
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
     * @magentoDataFixture loadOrders
     * @magentoConfigFixture current_store positive_indicators/recently_bought/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/recently_bought/period 7
     * @magentoConfigFixture current_store positive_indicators/recently_bought/minimal 2
     */
    public function testItReturnsCorrectProductsDataForSpecificSettings()
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

    public static function loadOrders()
    {
        require __DIR__ . '/../_files/orders.php';
    }

    public static function loadOrdersRollback()
    {
        require __DIR__ . '/../_files/orders_rollback.php';
    }
}
