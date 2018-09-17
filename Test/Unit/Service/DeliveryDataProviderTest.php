<?php

namespace MageSuite\ProductPositiveIndicators\Test\Unit\Service;

class DeliveryDataProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider
     */
    private $deliveryDataProvider;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->deliveryDataProvider = $objectManager->getObject(\MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider::class);
    }

    /**
     * @param array $config
     * @param string $date
     * @param array $excepted
     * @dataProvider dataProvider
     */
    public function testItReturnsCorrectData($config, $date, $excepted)
    {
        $deliveryData = $this->deliveryDataProvider->prepareDeliveryData($config, 'd.m.Y', $date);

        $this->assertEquals($excepted, $deliveryData);
    }

    public function dataProvider()
    {
        include __DIR__ . '/../_files/fast_shipping_scenarios.php';

        return $scenarios;
    }
}

