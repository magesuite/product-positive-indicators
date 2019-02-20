<?php

namespace MageSuite\ProductPositiveIndicators\Test\Unit\Service\DataProvider;

class FastShippingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping
     */
    private $fastShippingDataProvider;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->fastShippingDataProvider = $objectManager->getObject(\MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping::class);
    }

    /**
     * @param array $config
     * @param string $date
     * @param array $excepted
     * @dataProvider dataProvider
     */
    public function testItReturnsCorrectData($config, $date, $excepted)
    {
        $deliveryData = $this->fastShippingDataProvider->getDeliveryData($config, $date);

        $this->assertEquals($excepted, $deliveryData);
    }

    public function dataProvider()
    {
        include __DIR__ . '/../../_files/fast_shipping_scenarios.php';

        return $scenarios;
    }
}

