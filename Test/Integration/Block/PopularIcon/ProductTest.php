<?php

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Block\PopularIcon;

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
     * @var \MageSuite\ProductPositiveIndicators\Block\PopularIcon\Product
     */
    protected $productBlock;

    public function setUp()
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
     * @magentoDataFixture loadProducts
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     */
    public function testItReturnCorrectFlag($sku, $flag)
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
    public function testItReturnsFalseWhenNoCurrentProductIsRegistered()
    {
        $this->coreRegistry->register('product', null);

        $popularIconFlag = $this->productBlock->getPopularIconFlag();

        $this->assertNull($popularIconFlag);
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