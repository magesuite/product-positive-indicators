<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Unit\Block\FastShipping;

class ProductTest extends \PHPUnit\Framework\TestCase
{
    protected ?\MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping $configuration;
    protected ?\MageSuite\ProductPositiveIndicators\Block\FastShipping\Product $block;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->configuration = $objectManager->get(\MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping::class);
        $this->block = $objectManager->get(\MageSuite\ProductPositiveIndicators\Block\FastShipping\Product::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testIsEnabledDoesNotThrowWhenTheProviderFailsClosed(): void
    {
        $this->prepareConfiguration([
            'is_enabled' => 1,
            'working_days' => '1,2,3,4,5',
            'delivery_today_time' => '16:00',
            'working_hours' => 0,
            'order_queue_length' => 0
        ]);

        $this->assertNull($this->block->getMaxTimeToday());
        $this->assertNull($this->block->getShipDayTime());
        $this->assertNull($this->block->getNextShipDayName());
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testItCachesAFailedCalculationForTheRestOfTheRequest(): void
    {
        $this->prepareConfiguration([
            'is_enabled' => 1,
            'working_days' => '1,2,3,4,5',
            'delivery_today_time' => '16:00',
            'working_hours' => 0,
            'order_queue_length' => 0
        ]);

        $dataProvider = $this->createMock(\MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping::class);
        $dataProvider->expects($this->once())->method('getDeliveryData')->willReturn(null);

        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $block = $objectManager->create(\MageSuite\ProductPositiveIndicators\Block\FastShipping\Product::class, [
            'fastShippingDataProvider' => $dataProvider
        ]);

        $block->getMaxTimeToday();
        $block->getShipDayTime();
        $block->getNextShipDayName();
        $block->getUtcOffset();
    }

    protected function prepareConfiguration(array $testConfig): void
    {
        $config = $this->configuration->getConfig();

        foreach ($testConfig as $key => $value) {
            $config->setData($key, $value);
        }
    }
}
