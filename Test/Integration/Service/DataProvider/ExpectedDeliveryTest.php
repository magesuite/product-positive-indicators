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
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery
     */
    protected $configuration;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery
     */
    protected $expectedDeliveryDataProvider;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);

        $this->configuration = $objectManager->get(\MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery::class);
        $this->expectedDeliveryDataProvider = $objectManager->get(\MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadProducts
     * @param array $config
     * @param string $sku
     * @param array $excepted
     * @dataProvider dataProvider
     */
    public function testItReturnsCorrectData($config, $sku, $excepted)
    {
        $product = $this->productRepository->get($sku);

        $config = $this->prepareConfiguration($config);

        $deliveryData = $this->expectedDeliveryDataProvider->getDeliveryData($config, $product);

        if($excepted === null){
            $this->assertNull($deliveryData);
        }else{
            $this->assertEquals($excepted['deliveryDayName'], (string)$deliveryData['deliveryDayName']);
            $this->assertEquals($excepted['deliveryNextDayName'], (string)$deliveryData['deliveryNextDayName']);
        }
    }

    private function prepareConfiguration($testConfig)
    {
        $config = $this->configuration->getConfig(\MageSuite\ProductPositiveIndicators\Block\ExpectedDelivery\Product::XML_PATH_CONFIGURATION_KEY);

        foreach($testConfig as $key => $value){
            $config->setData($key, $value);
        }

        return $config;
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

