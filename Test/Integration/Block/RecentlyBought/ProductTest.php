<?php

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Block\RecentlyBought;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ProductTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Block\RecentlyBought\Product
     */
    protected $productBlock;

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
     * @magentoDataFixture loadProducts
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/recently_bought/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/recently_bought/period 7
     */
    public function testItReturnCorrectFlag($sku, $data)
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
    public function testItReturnsFalseWhenNoCurrentProductIsRegistered()
    {
        $this->coreRegistry->register('product', null);

        $recentlyBoughtInfo = $this->productBlock->getRecentlyBoughtInfo();

        $this->assertEquals(['active' => 0], $recentlyBoughtInfo);
    }

    public static function loadProducts()
    {
        require __DIR__ . '/../../_files/products.php';
    }

    public static function loadProductsRollback()
    {
        require __DIR__ . '/../../_files/products_rollback.php';
    }

    public static function getExpectedData()
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
