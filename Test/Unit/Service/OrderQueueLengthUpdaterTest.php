<?php

namespace MageSuite\ProductPositiveIndicators\Test\Unit\Service;

class OrderQueueLengthUpdaterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\OrderQueueLengthUpdater
     */
    private $orderQueueLengthUpdater;

    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->orderQueueLengthUpdater = $objectManager->getObject(\MageSuite\ProductPositiveIndicators\Service\OrderQueueLengthUpdater::class);
    }

    /**
     * @param boolean $flag
     * @param string $orderQueueLength
     * @dataProvider dataProvider
     */
    public function testItOnlyAcceptsIntegerAsQueueLengthValue($flag, $orderQueueLength)
    {
        $this->assertEquals($flag, $this->orderQueueLengthUpdater->updateOrderQueueLength($orderQueueLength));
    }

    public function dataProvider()
    {
        return [
            [true, 10],
            [false, 'test'],
            [false, '']
        ];
    }
}
