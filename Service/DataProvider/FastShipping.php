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

        $businessDay = $this->isWorkingDay($currentDateTime) && !$this->isHoliday($currentDateTime);

        if($businessDay and ($currentDateTime->getTimestamp() + $this->configuration->getOrderQueueLength()) < $maxTimeToday->getTimestamp()){
            return [
                'day' => 'today',
                'time' => ($maxTimeToday->getTimestamp() - $this->configuration->getOrderQueueLength()) - $this->configuration->getUtcOffset(),
                'deliveryDay' => __($maxTimeToday->format('l')),
                'utcOffset' => $this->configuration->getUtcOffset()
            ];
        }

        $timeLeft = $this->configuration->getOrderQueueLength() - ($maxTimeToday->getTimestamp() - $currentDateTime->getTimestamp());

        $deliveryDayAndNextDay = $this->getDeliveryDayAndNextDay($currentDateTime, $timeLeft);

        $deliveryDay = $deliveryDayAndNextDay['deliveryDay'];
        $dayType = $deliveryDay->format('d') == $deliveryDayAndNextDay['nextDay'] ? 'tomorrow' : 'other';

        $time = $deliveryDay->getTimestamp();

        if($dayType == 'tomorrow'){
            $deliveryDay = new \DateTime($deliveryDay->format('d.m.Y') . ' ' . $this->configuration->getDeliveryTodayTime());
            $time  = $deliveryDay->getTimestamp() - $this->configuration->getUtcOffset();

        }

        return [
            'day' => $dayType,
            'time' => $time,
            'deliveryDay' => __($deliveryDay->format('l')),
            'utcOffset' => $this->configuration->getUtcOffset()
        ];

    }

    protected function getDeliveryDayAndNextDay($currentTime, $timeLeft)
    {
        $nextBusinessDay = false;
        $nextDay = null;

        while (!$nextBusinessDay) {
            $currentTime->modify('+1 day');

            if(!$nextDay){
                $nextDay = $currentTime->format('d');
            }

            if(!$this->isWorkingDay($currentTime)){
                continue;
            }

            if($this->isHoliday($currentTime)){
                continue;
            }

            $timeLeft = $timeLeft - $this->configuration->getWorkingHours();

            if($timeLeft > 0){
                continue;
            }

            $nextBusinessDay = true;
        }

        return ['deliveryDay' => $currentTime, 'nextDay' => $nextDay];
    }
}