<?php

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Service;

class FreeShippingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\FreeShipping
     */
    private $freeShippingService;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->freeShippingService = $this->objectManager
            ->get(\MageSuite\ProductPositiveIndicators\Service\FreeShipping::class);

        $this->productRepository = $this->objectManager
            ->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadProducts
     */
    public function testIsFreeShippedReturnFalse()
    {
        $product = $product = $this->productRepository->getById(603);

        $value = $this->freeShippingService->isFreeShipped($product);
        $this->assertEquals(false, $value);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoConfigFixture current_store positive_indicators/free_shipping/free_shipping_method freeshipping
     * @magentoConfigFixture current_store general/country/default DE
     * @magentoConfigFixture current_store carriers/freeshipping/active 1
     * @magentoConfigFixture current_store carriers/freeshipping/sallowspecific 1
     * @magentoConfigFixture current_store carriers/freeshipping/specificcountry DE,US
     * @magentoConfigFixture current_store carriers/freeshipping/free_shipping_subtotal 49
     */
    public function testIsFreeShipped()
    {
        $product = $product = $this->productRepository->getById(604);

        $value = $this->freeShippingService->isFreeShipped($product);
        $this->assertEquals(true, $value);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoConfigFixture current_store positive_indicators/free_shipping/free_shipping_method freeshipping
     * @magentoConfigFixture current_store general/country/default PL
     * @magentoConfigFixture current_store carriers/freeshipping/active 1
     * @magentoConfigFixture current_store carriers/freeshipping/sallowspecific 1
     * @magentoConfigFixture current_store carriers/freeshipping/specificcountry DE,US
     * @magentoConfigFixture current_store carriers/freeshipping/free_shipping_subtotal 49
     */
    public function testIsNotFreeShippedByCountry()
    {
        $product = $product = $this->productRepository->getById(604);

        $value = $this->freeShippingService->isFreeShipped($product);
        $this->assertEquals(false, $value);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadProducts
     * @magentoConfigFixture current_store positive_indicators/free_shipping/free_shipping_method ups
     * @magentoConfigFixture current_store carriers/ups/active 1
     * @magentoConfigFixture current_store carriers/ups/sallowspecific 0
     * @magentoConfigFixture current_store carriers/ups/free_shipping_enable 1
     * @magentoConfigFixture current_store carriers/ups/free_shipping_subtotal 50
     */
    public function testIsNotFreeShipped()
    {
        $product = $product = $this->productRepository->getById(603);

        $value = $this->freeShippingService->isFreeShipped($product);
        $this->assertEquals(false, $value);
    }


    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture current_store carriers/ups/active 1
     * @magentoConfigFixture current_store carriers/ups/sallowspecific 0
     * @magentoConfigFixture current_store carriers/ups/free_shipping_enable 1
     * @magentoConfigFixture current_store carriers/ups/free_shipping_subtotal 20
     * @magentoConfigFixture current_store carriers/freeshipping/active 1
     * @magentoConfigFixture current_store carriers/freeshipping/sallowspecific 1
     * @magentoConfigFixture current_store carriers/freeshipping/specificcountry DE,US
     * @magentoConfigFixture current_store carriers/freeshipping/free_shipping_subtotal 49
     */
    public function testGetShippingMethodsWithFreeShipping()
    {
        $value = $this->freeShippingService->getShippingMethodsWithFreeShipping();

        $this->assertArrayHasKey('freeshipping', $value);
        $this->assertEquals('Free Shipping', $value['freeshipping']['title']);
        $this->assertEquals('49', $value['freeshipping']['value']);
    }

    public static function loadProducts()
    {
        require __DIR__ . '/../_files/products.php';
    }

    public static function loadProductsRollback()
    {
        require __DIR__ . '/../_files/products_rollback.php';
    }
}
