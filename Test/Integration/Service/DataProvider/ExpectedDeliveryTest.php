<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Service\DataProvider;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ExpectedDeliveryTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected ?\MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery $configuration;
    protected ?\MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery $expectedDeliveryDataProvider;

    protected function setUp(): void
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
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('dataProvider')]
    public function testItReturnsCorrectData(array $config, string $sku, ?array $excepted): void
    {
        $product = $this->productRepository->get($sku);

        $this->prepareConfiguration($config);

        $deliveryData = $this->expectedDeliveryDataProvider->getDeliveryData($product);

        if ($excepted === null) {
            $this->assertNull($deliveryData);
            return;
        }

        $this->assertEquals($excepted['shipDayName'], (string)$deliveryData->getShipDayName());
        $this->assertEquals($excepted['nextShipDayName'], (string)$deliveryData->getNextShipDayName());
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    public function testItReturnsNoDataForANegativeShippingTime(): void
    {
        $product = $this->productRepository->get('custom_product');
        $product->setUseTimeNeededToShipProduct(1);
        $product->setTimeNeededToShipProduct(-1);

        $this->prepareConfiguration([
            'working_days' => '1,2,3,4,5',
            'holidays' => '',
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2,
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ]);

        $this->assertSame(0, $this->expectedDeliveryDataProvider->getShippingTimeInDays($product));
        $this->assertNull($this->expectedDeliveryDataProvider->getDeliveryData($product));
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    public function testItReturnsNoDataForAShippingTimeAboveTheSupportedHorizon(): void
    {
        $product = $this->productRepository->get('custom_product');
        $product->setUseTimeNeededToShipProduct(1);
        $product->setTimeNeededToShipProduct(
            \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery::MAX_SHIPPING_TIME_IN_DAYS + 1
        );

        $this->prepareConfiguration([
            'working_days' => '1,2,3,4,5',
            'holidays' => '',
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2,
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ]);

        $this->assertSame(0, $this->expectedDeliveryDataProvider->getShippingTimeInDays($product));
        $this->assertNull($this->expectedDeliveryDataProvider->getDeliveryData($product));
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    public function testItFailsClosedWhenNoWorkingDayIsConfigured(): void
    {
        $product = $this->productRepository->get('custom_product');

        $this->prepareConfiguration([
            'working_days' => '',
            'holidays' => '',
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2,
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ]);

        $this->assertNull($this->expectedDeliveryDataProvider->getDeliveryData($product));
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    public function testItConvergesAtTheMaximumHorizonWithASparseCalendar(): void
    {
        // Reaching the maximum accepted shipping time (365 working days) with only one
        // working day a week needs roughly 2,555 calendar days; a fixed 1,000-iteration
        // ceiling would wrongly fail closed on this legitimate, validation-permitted value.
        $product = $this->productRepository->get('custom_product');
        $product->setUseTimeNeededToShipProduct(1);
        $product->setTimeNeededToShipProduct(
            \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery::MAX_SHIPPING_TIME_IN_DAYS
        );

        $this->prepareConfiguration([
            'working_days' => '1',
            'holidays' => '',
            'delivery_today_time' => '15:00',
            'default_shipping_time' => 2,
            'timestamp' => 1521201600,
            'utc_offset' => 0
        ]);

        $deliveryData = $this->expectedDeliveryDataProvider->getDeliveryData($product);

        $this->assertNotNull($deliveryData);
    }

    public function testGetNumberOfBusinessDays(): void
    {
        $from = new \DateTime('2023-05-17');
        $to = new \DateTime('2023-05-29');

        $result = $this->expectedDeliveryDataProvider->getNumberOfBusinessDays($from, $to);

        $this->assertEquals(8, $result);
    }

    protected function prepareConfiguration(array $testConfig): void
    {
        $config = $this->configuration->getConfig();

        foreach ($testConfig as $key => $value) {
            $config->setData($key, $value);
        }
    }

    public static function dataProvider(): array
    {
        return [
            [
                [
                    'working_days' => '1,2,3,4,5',
                    'holidays' => '14.03.2018
            19.03.2018',
                    'delivery_today_time' => '15:00',
                    'default_shipping_time' => 2,
                    'timestamp' => 1521201600,
                    'utc_offset' => 0
                ],
                'simple_product',
                ['shipDayName' => 'Wednesday', 'nextShipDayName' => 'Thursday']
            ],
            [
                [
                    'working_days' => '1,2,3,4,5',
                    'holidays' => '14.03.2018
            19.03.2018',
                    'delivery_today_time' => '15:00',
                    'default_shipping_time' => 2,
                    'timestamp' => 1521374400,
                    'utc_offset' => 0
                ],
                'simple_product',
                ['shipDayName' => 'Wednesday', 'nextShipDayName' => 'Thursday']
            ],
            [
                [
                    'working_days' => '1,2,3,4,5',
                    'holidays' => '21.03.2018
            22.03.2018
            25.03.2018',
                    'delivery_today_time' => '15:00',
                    'default_shipping_time' => 2,
                    'timestamp' => 1521374400,
                    'utc_offset' => 0
                ],
                'simple_product',
                ['shipDayName' => 'Tuesday', 'nextShipDayName' => 'Friday']
            ],
            [
                [
                    'working_days' => '1,2,3,4,5',
                    'holidays' => '22.03.2018
            23.03.2018
            25.03.2018',
                    'delivery_today_time' => '15:00',
                    'default_shipping_time' => 2,
                    'timestamp' => 1521374400,
                    'utc_offset' => 0
                ],
                'out_of_stock',
                null
            ],
            [
                [
                    'working_days' => '1,2,3,4,5',
                    'holidays' => '14.03.2018
            19.03.2018',
                    'delivery_today_time' => '15:00',
                    'default_shipping_time' => 2,
                    'timestamp' => 1521201600,
                    'utc_offset' => 0
                ],
                'custom_product',
                ['shipDayName' => 'Friday', 'nextShipDayName' => 'Monday']
            ],
            [
                [
                    'working_days' => '0,1,2,3,4,5,6',
                    'holidays' => '14.03.2018
            19.03.2018',
                    'delivery_today_time' => '15:00',
                    'default_shipping_time' => 2,
                    'timestamp' => 1521201600,
                    'utc_offset' => 0
                ],
                'custom_product',
                ['shipDayName' => 'Wednesday', 'nextShipDayName' => 'Thursday']
            ],
        ];
    }
}
