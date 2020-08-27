<?php

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Helper;

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
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productHelper = $this->objectManager->get(\MageSuite\ProductPositiveIndicators\Helper\Product::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadProducts
     * @dataProvider getExpectedData
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     */
    public function testItReturnsCorrectFlag($productId, $flag)
    {
        $popularIconFlag = $this->productHelper->getPopularIconFlag($productId);

        $this->assertEquals($flag, $popularIconFlag);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 0
     */
    public function testItReturnsFalseIfConfigurationIsNotSet()
    {
        $popularIconFlag = $this->productHelper->getPopularIconFlag(601);

        $this->assertFalse($popularIconFlag);
    }


    public static function loadProducts()
    {
        require __DIR__ . '/../_files/products.php';
    }

    public static function loadProductsRollback()
    {
        require __DIR__ . '/../_files/products_rollback.php';
    }

    public static function getExpectedData() {
        return [
            [600, false],
            [601, true],
            [602, false],
        ];
    }
}
