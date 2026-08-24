<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Service\DataProvider;

class FastShipping extends \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    protected const int ABSOLUTE_MAX_CALENDAR_ITERATIONS = 200000;

    public function __construct( // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping $configuration
    ) {
        parent::__construct($configuration);
    }

    public function getDeliveryData(): ?\Magento\Framework\DataObject
    {
        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp((int)$this->configuration->getTimestamp());

        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $this->configuration->getDeliveryTodayTime());

        $timeLeft = max(0, $maxTimeToday->getTimestamp() - $currentDateTime->getTimestamp());
        $timeLeft = $this->configuration->getOrderQueueLength() - $timeLeft;

        $isBusinessDay = $this->isWorkingDay($currentDateTime) && !$this->isHoliday($currentDateTime);

        if (!$isBusinessDay) {
            $midnight = sprintf('%s 00:00:00', $currentDateTime->format('d.m.Y'));
            $maxTimeToday = new \DateTime($midnight);
        }

        $nextShippingDay = $this->getNextShippingDay($currentDateTime, (int)$timeLeft);

        if ($nextShippingDay === null) {
            return null;
        }

        return new \Magento\Framework\DataObject([
            'max_today_time' => $maxTimeToday->getTimestamp() - $this->configuration->getOrderQueueLength(),
            'ship_day_time' => $currentDateTime->getTimestamp(),
            'ship_day_name' => (string)__($currentDateTime->format('l')),
            'is_next_day_tomorrow' => $nextShippingDay->getIsNextDayTomorrow(),
            'next_ship_day_time' => $nextShippingDay->getShipDay()->getTimestamp(),
            'next_ship_day_name' => (string)__($nextShippingDay->getShipDay()->format('l'))
        ]);
    }

    protected function getNextShippingDay(\DateTime $currentTime, int $timeLeft): ?\Magento\Framework\DataObject
    {
        $workingHours = (int)$this->configuration->getWorkingHours();

        if ($workingHours <= 0) {
            return null;
        }

        $dayTime = clone $currentTime;
        $nextDay = null;
        $maxIterations = $this->getMaxCalendarIterations($timeLeft, $workingHours);

        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            $dayTime->modify('+1 day');

            if ($nextDay === null) {
                $nextDay = $dayTime->format('d');
            }

            if (!$this->isWorkingDay($dayTime) || $this->isHoliday($dayTime)) {
                continue;
            }

            $timeLeft -= $workingHours;

            if ($timeLeft > 0) {
                continue;
            }

            return new \Magento\Framework\DataObject([
                'ship_day' => $dayTime,
                'is_next_day_tomorrow' => $dayTime->format('d') === $nextDay
            ]);
        }

        return null;
    }

    protected function getMaxCalendarIterations(int $timeLeftInSeconds, int $workingHoursInSeconds): int
    {
        $businessDaysNeeded = (int)ceil(max(0, $timeLeftInSeconds) / $workingHoursInSeconds) + 1;

        $workingDaysPerWeek = max(1, count($this->configuration->getWorkingDays()));
        $calendarDaysNeeded = (int)ceil($businessDaysNeeded / $workingDaysPerWeek * 7);

        return min($calendarDaysNeeded + 366, self::ABSOLUTE_MAX_CALENDAR_ITERATIONS);
    }
}
