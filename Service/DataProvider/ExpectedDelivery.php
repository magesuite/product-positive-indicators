<?php

namespace MageSuite\ProductPositiveIndicators\Service\DataProvider;

class ExpectedDelivery extends \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    public function getDeliveryData($config, $product = null, $specificDate = null)
    {
        $isInStock = $product->getQuantityAndStockStatus()['is_in_stock'];

        if(empty($isInStock)){
            return null;
        }

        $config = $this->getConfigurationForIndicator($config, $specificDate);

        $shippingTimeInDays = $this->getShippingTimeInDays($config, $product);

        if(!$shippingTimeInDays){
            return null;
        }

        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp($config['timestamp']);
        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $config['delivery_today_time']);

        $canShipToday = $this->isWorkingDay($config, $currentDateTime) && $this->isNotHoliday($config, $currentDateTime);
        $shippingDays = $this->getShippingDays($config, $currentDateTime, $shippingTimeInDays);

        return [
            'maxTodayTime' => $canShipToday ? $maxTimeToday->getTimestamp() : null,
            'deliveryDayTime' => $shippingDays['delivery_day']->getTimestamp(),
            'deliveryDayName' => __($shippingDays['delivery_day']->format('l')),
            'deliveryNextDayTime' => $shippingDays['next_delivery_day']->getTimestamp(),
            'deliveryNextDayName' => __($shippingDays['next_delivery_day']->format('l')),
            'utcOffset' => $config['utc_offset']
        ];

    }

    protected function getShippingTimeInDays($config, $product)
    {
        $shippingTime = $config['default_shipping_time'] ?? 0;

        if($product->getUseSpecificShippingTime()){
            $shippingTime = $product->getSpecificShippingTime() ? $product->getSpecificShippingTime() : $shippingTime;
        }

        return $shippingTime;
    }

    protected function getShippingDays($config, $currentDay, $shippingTimeInDays)
    {
        $deliveryDay = null;

        while ($shippingTimeInDays) {
            $currentDay->modify('+1 day');

            $isBusinessDay = $this->isWorkingDay($config, $currentDay) && $this->isNotHoliday($config, $currentDay);

            if(!$isBusinessDay) {
                continue;
            }

            $shippingTimeInDays--;
            $deliveryDay = $currentDay;
        }

        $nextDeliveryDay = null;
        $dateTime = new \DateTime('now');
        $dateTime->setTimestamp($deliveryDay->getTimestamp());

        while (!$nextDeliveryDay){
            $dateTime->modify('+1 day');

            $isBusinessDay = $this->isWorkingDay($config, $dateTime) && $this->isNotHoliday($config, $dateTime);

            if(!$isBusinessDay) {
                continue;
            }

            $nextDeliveryDay = $dateTime;
        }

        return ['delivery_day' => $deliveryDay, 'next_delivery_day' => $nextDeliveryDay];
    }

    protected function getConfigurationForIndicator($config, $specificDate)
    {
        $config = $this->getConfiguration($config);
        $config['timestamp'] = $specificDate ? strtotime($specificDate) : $this->localeDate->scopeTimeStamp();

        return $config;
    }


}