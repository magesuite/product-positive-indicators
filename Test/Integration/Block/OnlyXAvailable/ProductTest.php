<?php

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Block\OnlyXAvailable;

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
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $stockInterface;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Block\OnlyXAvailable\Product
     */
    protected $productBlock;

    public function setUp()
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
     * @magentoDataFixture loadProducts
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/only_x_available/active 1
     * @magentoConfigFixture current_store positive_indicators/only_x_available/quantity 10
     */
    public function testItReturnsCorrectFlag($sku, $flag)
    {
        $product = $this->productRepository->get($sku);
        $this->coreRegistry->register('product', $product);

        $displayInfo = $this->productBlock->displayInfoOnProductPage();

        $this->assertEquals($flag, $displayInfo);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadProducts
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/only_x_available/active 1
     * @magentoConfigFixture current_store positive_indicators/only_x_available/quantity 10
     */
    public function testItReturnsCorrectFlagForQtyParameter($sku, $flag)
    {
        $product = $this->productRepository->get($sku);
        $productQty = $this->stockInterface->getStockQty($product->getId());

        $displayInfo = $this->productBlock->displayInfoOnProductPage($productQty);

        $this->assertEquals($flag, $displayInfo);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoConfigFixture current_store positive_indicators/only_x_available/active 1
     */
    public function testItReturnsFalseIfConfigurationIsNotSet()
    {
        $product = $this->productRepository->get('product_qty_100');
        $this->coreRegistry->register('product', $product);

        $displayInfo = $this->productBlock->displayInfoOnProductPage();

        $this->assertFalse($displayInfo);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store positive_indicators/only_x_available/active 1
     */
    public function testItReturnsFalseWhenNoCurrentProductIsRegistered()
    {
        $this->coreRegistry->register('product', null);

        $displayInfo = $this->productBlock->displayInfoOnProductPage();

        $this->assertFalse($displayInfo);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoConfigFixture current_store positive_indicators/only_x_available/active 1
     * @magentoConfigFixture current_store positive_indicators/only_x_available/quantity 10
     */
    public function testItReturnsCorrectFlagForQtyParameterFromProduct()
    {
        $product = $this->productRepository->get('product_qty_available');
        $this->coreRegistry->register('product', $product);

        $displayInfo = $this->productBlock->displayInfoOnProductPage();

        $this->assertTrue($displayInfo);
    }


    public static function loadProducts()
    {
        require __DIR__ . '/../../_files/products.php';
    }

    public static function loadProductsRollback()
    {
        require __DIR__ . '/../../_files/products_rollback.php';
    }

    public static function getExpectedData() {
        return [
            ['product_qty_100', false],
            ['product_qty_2', true],
            ['product_qty_0', false],
        ];
    }
}
