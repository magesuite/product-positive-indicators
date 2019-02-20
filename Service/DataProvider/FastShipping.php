<?php

namespace MageSuite\ProductPositiveIndicators\Service\DataProvider;

class FastShipping extends \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    public function getDeliveryData($config, $specificDate = null)
    {
        $config = $this->getConfigurationForIndicator($config, $specificDate);

        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp($config['timestamp']);

        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $config['delivery_today_time']);

        $businessDay = $this->isWorkingDay($config, $currentDateTime) && $this->isNotHoliday($config, $currentDateTime);

        if($businessDay and ($currentDateTime->getTimestamp() + $config['order_queue_length']) < $maxTimeToday->getTimestamp()){
            return [
                'day' => 'today',
                'time' => ($maxTimeToday->getTimestamp() - $config['order_queue_length']) - $config['utc_offset'],
                'deliveryDay' => __($maxTimeToday->format('l')),
                'utcOffset' => $config['utc_offset']
            ];
        }

        $timeLeft = $config['order_queue_length'] - ($maxTimeToday->getTimestamp() - $currentDateTime->getTimestamp());

        $deliveryDayAndNextDay = $this->getDeliveryDayAndNextDay($config, $currentDateTime, $timeLeft);

        $deliveryDay = $deliveryDayAndNextDay['deliveryDay'];
        $dayType = $deliveryDay->format('d') == $deliveryDayAndNextDay['nextDay'] ? 'tomorrow' : 'other';

        $time = $deliveryDay->getTimestamp();

        if($dayType == 'tomorrow'){
            $deliveryDay = new \DateTime($deliveryDay->format('d.m.Y') . ' ' . $config['delivery_today_time']);
            $time  = $deliveryDay->getTimestamp() - $config['utc_offset'];

        }

        return [
            'day' => $dayType,
            'time' => $time,
            'deliveryDay' => __($deliveryDay->format('l')),
            'utcOffset' => $config['utc_offset']
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

            if (!in_array($currentTime->format('N'), $config['working_days'])){
                continue;
            }

            if (in_array($currentTime->format('d.m.Y'), $config['holidays'])){
                continue;
            }

            $timeLeft = $timeLeft - $config['working_hours'];

            if($timeLeft > 0){
                continue;
            }

            $nextBusinessDay = true;
        }

        return ['deliveryDay' => $currentTime, 'nextDay' => $nextDay];
    }

    protected function getConfigurationForIndicator($config, $specificDate)
    {
        $config = $this->getConfiguration($config);

        $config['working_hours'] = $this->getDataFromConfiguration($config['working_hours'], 'hours');
        $config['order_queue_length'] = $this->getDataFromConfiguration($config['order_queue_length'], 'hours');
        $config['timestamp'] = $specificDate ? strtotime($specificDate) : $this->localeDate->scopeTimeStamp();

        return $config;
    }
}