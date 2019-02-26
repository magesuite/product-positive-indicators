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
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration
     */
    protected $configurationStub;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery
     */
    protected $expectedDeliveryDataProvider;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);

        $this->configurationStub = $this
            ->getMockBuilder(\MageSuite\ProductPositiveIndicators\Helper\Configuration::class)
            ->setConstructorArgs([
                $objectManager->get(\Magento\Framework\App\Helper\Context::class),
                $objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class),
                $objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class),
                $objectManager->get(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class)
            ])
            ->setMethods(['getConfigFromDatabase'])
            ->getMock();

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

        $this->configurationStub->method('getConfigFromDatabase')->willReturn($config);
        $preparedConfig = $this->configurationStub->getConfig('test');

        $deliveryData = $this->expectedDeliveryDataProvider->getDeliveryData($preparedConfig, $product);

        if($excepted === null){
            $this->assertNull($deliveryData);
        }else{
            $this->assertEquals($excepted['deliveryDayName'], (string)$deliveryData['deliveryDayName']);
            $this->assertEquals($excepted['deliveryNextDayName'], (string)$deliveryData['deliveryNextDayName']);
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

