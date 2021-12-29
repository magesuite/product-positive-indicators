<?php

namespace MageSuite\ProductPositiveIndicators\Test\Unit\Service\DataProvider;

class FastShippingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping
     */
    protected $configuration;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping
     */
    protected $fastShippingDataProvider;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->configuration = $objectManager->get(\MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping::class);
        $this->fastShippingDataProvider = $objectManager->get(\MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @param array $config
     * @param array $excepted
     * @dataProvider dataProvider
     */
    public function testItReturnsCorrectData($config, $excepted)
    {
        $this->prepareConfiguration($config);

        $deliveryData = $this->fastShippingDataProvider->getDeliveryData();

        $this->assertEquals($excepted['shipDayName'], (string)$deliveryData->getShipDayName());
        $this->assertEquals($excepted['nextShipDayName'], (string)$deliveryData->getNextShipDayName());
        $this->assertEquals($excepted['maxTodayTime'], $deliveryData->getMaxTodayTime());
        $this->assertEquals($excepted['isNextDayTomorrow'], $deliveryData->getIsNextDayTomorrow());
    }

    private function prepareConfiguration($testConfig)
    {
        $config = $this->configuration->getConfig();

        foreach ($testConfig as $key => $value) {
            $config->setData($key, $value);
        }
    }

    public function dataProvider()
    {
        include __DIR__ . '/../../_files/fast_shipping_scenarios.php';

        return $scenarios;
    }
}
