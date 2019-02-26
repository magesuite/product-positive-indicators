<?php

namespace MageSuite\ProductPositiveIndicators\Service\DataProvider;

class FastShipping extends \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    public function getDeliveryData($config)
    {
        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp($config->getTimestamp());

        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $config->getDeliveryTodayTime());

        $businessDay = $this->isWorkingDay($config, $currentDateTime) && !$this->isHoliday($config, $currentDateTime);

        if($businessDay and ($currentDateTime->getTimestamp() + $config->getOrderQueueLength()) < $maxTimeToday->getTimestamp()){
            return [
                'day' => 'today',
                'time' => ($maxTimeToday->getTimestamp() - $config->getOrderQueueLength()) - $config->getUtcOffset(),
                'deliveryDay' => __($maxTimeToday->format('l')),
                'utcOffset' => $config->getUtcOffset()
            ];
        }

        $timeLeft = $config->getOrderQueueLength() - ($maxTimeToday->getTimestamp() - $currentDateTime->getTimestamp());

        $deliveryDayAndNextDay = $this->getDeliveryDayAndNextDay($config, $currentDateTime, $timeLeft);

        $deliveryDay = $deliveryDayAndNextDay['deliveryDay'];
        $dayType = $deliveryDay->format('d') == $deliveryDayAndNextDay['nextDay'] ? 'tomorrow' : 'other';

        $time = $deliveryDay->getTimestamp();

        if($dayType == 'tomorrow'){
            $deliveryDay = new \DateTime($deliveryDay->format('d.m.Y') . ' ' . $config->getDeliveryTodayTime());
            $time  = $deliveryDay->getTimestamp() - $config->getUtcOffset();

        }

        return [
            'day' => $dayType,
            'time' => $time,
            'deliveryDay' => __($deliveryDay->format('l')),
            'utcOffset' => $config->getUtcOffset()
        ];

    }

    protected function getDeliveryDayAndNextDay($config, $currentTime, $timeLeft)
    {
        $nextBusinessDay = false;
        $nextDay = null;

        while (!$nextBusinessDay) {
            $currentTime->modify('+1 day');

            if(!$nextDay){
                $nextDay = $currentTime->format('d');
            }

            if(!$this->isWorkingDay($config, $currentTime)){
                continue;
            }

            if($this->isHoliday($config, $currentTime)){
                continue;
            }

            $timeLeft = $timeLeft - $config->getWorkingHours();

            if($timeLeft > 0){
                continue;
            }

            $nextBusinessDay = true;
        }

        return ['deliveryDay' => $currentTime, 'nextDay' => $nextDay];
    }
}