<?php

namespace MageSuite\ProductPositiveIndicators\Test\Unit\Service\DataProvider;

class FastShippingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration
     */
    protected $configurationStub;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping
     */
    protected $fastShippingDataProvider;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();


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

        $this->fastShippingDataProvider = $objectManager->get(\MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @param array $config
     * @param array $excepted
     * @dataProvider dataProvider
     */
    public function testItReturnsCorrectData($config, $excepted)
    {
        $this->configurationStub->method('getConfigFromDatabase')->willReturn($config);
        $preparedConfig = $this->configurationStub->getConfig('test');

        $deliveryData = $this->fastShippingDataProvider->getDeliveryData($preparedConfig);

        $this->assertEquals($excepted, $deliveryData);
    }

    public function dataProvider()
    {
        include __DIR__ . '/../../_files/fast_shipping_scenarios.php';

        return $scenarios;
    }
}

