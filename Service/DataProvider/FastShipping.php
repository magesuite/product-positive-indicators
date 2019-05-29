<?php

namespace MageSuite\ProductPositiveIndicators\Service\DataProvider;

class FastShipping extends \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping
     */
    protected $configuration;

    public function __construct(
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping $configuration
    ){
        parent::__construct($configuration);
    }

    public function getDeliveryData()
    {
        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp($this->configuration->getTimestamp());

        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $this->configuration->getDeliveryTodayTime());

        $timeLeft = max(0, $maxTimeToday->getTimestamp() - $currentDateTime->getTimestamp());
        $timeLeft = $this->configuration->getOrderQueueLength() - $timeLeft;

        $isBusinessDay = $this->isWorkingDay($currentDateTime) && !$this->isHoliday($currentDateTime);

        if(!$isBusinessDay){
            $midnight = sprintf('%s 00:00:00', $currentDateTime->format('d.m.Y'));
            $maxTimeToday = new \DateTime($midnight);
        }

        $nextShippingDay = $this->getNextShippingDay($currentDateTime, $timeLeft);

        return new \Magento\Framework\DataObject([
            'max_today_time' => $maxTimeToday->getTimestamp() - $this->configuration->getOrderQueueLength(),
            'ship_day_time' => $currentDateTime->getTimestamp(),
            'ship_day_name' => __($currentDateTime->format('l')),
            'is_next_day_tomorrow' => $nextShippingDay->getIsNextDayTomorrow(),
            'next_ship_day_time' => $nextShippingDay->getShipDay()->getTimestamp(),
            'next_ship_day_name' => __($nextShippingDay->getShipDay()->format('l'))
        ]);
    }

    protected function getNextShippingDay($currentTime, $timeLeft)
    {
        $dayTime = clone $currentTime;
        $nextBusinessDay = false;
        $nextDay = null;

        while (!$nextBusinessDay) {
            $dayTime->modify('+1 day');

            if(!$nextDay){
                $nextDay = $dayTime->format('d');
            }

            if(!$this->isWorkingDay($dayTime)){
                continue;
            }

            if($this->isHoliday($dayTime)){
                continue;
            }

            $timeLeft = $timeLeft - $this->configuration->getWorkingHours();

            if($timeLeft > 0){
                continue;
            }

            $nextBusinessDay = true;
        }

        return new \Magento\Framework\DataObject([
            'ship_day' => $dayTime,
            'is_next_day_tomorrow' => $dayTime->format('d') == $nextDay ? true : false
        ]);
    }
}