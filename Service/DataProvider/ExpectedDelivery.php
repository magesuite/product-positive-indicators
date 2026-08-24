<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Service\DataProvider;

class ExpectedDelivery extends \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    public const MIN_SHIPPING_TIME_IN_DAYS = 1;
    public const MAX_SHIPPING_TIME_IN_DAYS = 365;

    protected const ABSOLUTE_MAX_CALENDAR_ITERATIONS = 200000;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

    public function __construct(
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery $configuration,
        \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper
    ) {
        parent::__construct($configuration);
        $this->productHelper = $productHelper;
    }

    public function getDeliveryData($product = null): ?\Magento\Framework\DataObject
    {
        if (!$product->isSalable()) {
            return null;
        }

        $shippingTimeInDays = $this->getShippingTimeInDays($product);
        if (!$shippingTimeInDays) {
            return null;
        }

        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp((int)$this->configuration->getTimestamp());
        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $this->configuration->getDeliveryTodayTime());

        $canShipToday = $this->isWorkingDay($currentDateTime) && !$this->isHoliday($currentDateTime);
        $shippingDays = $this->getShippingDays($currentDateTime, $shippingTimeInDays);

        if ($shippingDays === null) {
            return null;
        }

        return new \Magento\Framework\DataObject([
            'max_today_time' => $canShipToday ? $maxTimeToday->getTimestamp() : null,
            'ship_day_time' => $shippingDays->getShipDay()->getTimestamp(),
            'ship_day_name' => (string)__($shippingDays->getShipDay()->format('l')),
            'next_ship_day_time' => $shippingDays->getNextShipDay()->getTimestamp(),
            'next_ship_day_name' => (string)__($shippingDays->getNextShipDay()->format('l')),
            'utc_offset' => $this->configuration->getUtcOffset()
        ]);
    }

    /**
     * @return int - Shipping time in working days, or 0 if unset or outside the supported range
     */
    public function getShippingTimeInDays(\Magento\Catalog\Api\Data\ProductInterface $product): int
    {
        $shippingTimeInDays = $product->getUseTimeNeededToShipProduct()
            ? (int)$product->getTimeNeededToShipProduct()
            : (int)$this->configuration->getDefaultShippingTime();

        if ($shippingTimeInDays < self::MIN_SHIPPING_TIME_IN_DAYS || $shippingTimeInDays > self::MAX_SHIPPING_TIME_IN_DAYS) {
            return 0;
        }

        return $shippingTimeInDays;
    }

    protected function getShippingDays(\DateTime $currentDay, int $shippingTimeInDays): ?\Magento\Framework\DataObject
    {
        $shipDay = $this->findShipDay($currentDay, $shippingTimeInDays);

        if ($shipDay === null) {
            return null;
        }

        $nextShipDay = $this->findNextBusinessDay($shipDay);

        if ($nextShipDay === null) {
            return null;
        }

        return new \Magento\Framework\DataObject([
            'ship_day' => $shipDay,
            'next_ship_day' => $nextShipDay
        ]);
    }

    protected function findShipDay(\DateTime $currentDay, int $shippingTimeInDays): ?\DateTime
    {
        $shipDay = null;
        $maxIterations = $this->getMaxCalendarIterations($shippingTimeInDays);

        for ($iteration = 0; $shippingTimeInDays > 0 && $iteration < $maxIterations; $iteration++) {
            $currentDay->modify('+1 day');

            $isBusinessDay = $this->isWorkingDay($currentDay) && !$this->isHoliday($currentDay);

            if (!$isBusinessDay) {
                continue;
            }

            $shippingTimeInDays--;
            $shipDay = $currentDay;
        }

        return $shippingTimeInDays > 0 ? null : $shipDay;
    }

    protected function findNextBusinessDay(\DateTime $shipDay): ?\DateTime
    {
        $dateTime = new \DateTime('now');
        $dateTime->setTimestamp($shipDay->getTimestamp());
        $maxIterations = $this->getMaxCalendarIterations(1);

        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            $dateTime->modify('+1 day');

            $isBusinessDay = $this->isWorkingDay($dateTime) && !$this->isHoliday($dateTime);

            if (!$isBusinessDay) {
                continue;
            }

            return $dateTime;
        }

        return null;
    }

    protected function getMaxCalendarIterations(int $businessDaysNeeded): int
    {
        $workingDaysPerWeek = max(1, count($this->configuration->getWorkingDays()));
        $calendarDaysNeeded = (int)ceil($businessDaysNeeded / $workingDaysPerWeek * 7);

        return min($calendarDaysNeeded + 366, self::ABSOLUTE_MAX_CALENDAR_ITERATIONS);
    }
}
