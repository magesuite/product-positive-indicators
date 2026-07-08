<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Unit\Service;

class OrderQueueLengthUpdaterTest extends \PHPUnit\Framework\TestCase
{
    protected ?\MageSuite\ProductPositiveIndicators\Service\OrderQueueLengthUpdater $orderQueueLengthUpdater;

    protected function setUp(): void
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->orderQueueLengthUpdater = $objectManager->getObject(\MageSuite\ProductPositiveIndicators\Service\OrderQueueLengthUpdater::class);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dataProvider')]
    public function testItOnlyAcceptsIntegerAsQueueLengthValue(bool $flag, mixed $orderQueueLength): void
    {
        $this->assertEquals($flag, $this->orderQueueLengthUpdater->updateOrderQueueLength($orderQueueLength));
    }

    public static function dataProvider(): array
    {
        return [
            [true, 10],
            [false, 'test'],
            [false, '']
        ];
    }
}
