<?php

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Service\DataProvider;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ExpectedDeliveryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery
     */
    protected $expectedDeliveryDataProvider;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->expectedDeliveryDataProvider = $objectManager->get(\MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadProducts
     * @param array $config
     * @param string $date
     * @param string $sku
     * @param array $excepted
     * @dataProvider dataProvider
     */
    public function testItReturnsCorrectData($config, $date, $sku, $excepted)
    {
        $product = $this->productRepository->get($sku);
        $deliveryData = $this->expectedDeliveryDataProvider->prepareDeliveryData($config, $product, $date);

        if($excepted === null){
            $this->assertNull($deliveryData);
        }else{
            $this->assertEquals($excepted['deliveryDayName'], (string)$deliveryData['deliveryDayName']);
            $this->assertEquals($excepted['nextDeliveryDayName'], (string)$deliveryData['nextDeliveryDayName']);
        }
    }

    public function dataProvider()
    {
        include __DIR__ . '/../../_files/expected_delivery_scenarios.php';

        return $scenarios;
    }

    public static function loadProducts()
    {
        require __DIR__ . '/../../_files/expected_delivery_products.php';
    }

    public static function loadProductsRollback()
    {
        require __DIR__ . '/../../_files/expected_delivery_products_rollback.php';
    }
}

