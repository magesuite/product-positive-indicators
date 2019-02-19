<?php

namespace MageSuite\ProductPositiveIndicators\Service\DataProvider;

class ExpectedDelivery extends \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    public function prepareDeliveryData($config, $product = null, $specificDate = null)
    {
        $isInStock = $product->getQuantityAndStockStatus()['is_in_stock'];

        if(empty($isInStock)){
            return null;
        }

        $config = $this->prepareConfigurationForIndicator($config, $specificDate);

        $leadTime = $this->prepareLeadTime($config, $product);

        if(!$leadTime){
            return null;
        }

        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp($config['timestamp']);
        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $config['delivery_today_time']);

        $canShipToday = $this->isBusinessDay($config, $currentDateTime);
        $shippingDays = $this->prepareShippingDays($config, $currentDateTime, $leadTime);

        return [
            'maxTodayTime' => $canShipToday ? $maxTimeToday->getTimestamp() : null,
            'deliveryDayTime' => $shippingDays['delivery_day']->getTimestamp(),
            'deliveryDayName' => __($shippingDays['delivery_day']->format('l')),
            'nextDeliveryDayTime' => $shippingDays['next_delivery_day']->getTimestamp(),
            'nextDeliveryDayName' => __($shippingDays['next_delivery_day']->format('l')),
            'utcOffset' => $config['utc_offset']
        ];

    }

    protected function prepareLeadTime($config, $product)
    {
        $leadTime = $config['default_lead_time'] ?? 0;

        if($product->getUseSpecificLeadTime()){
            $leadTime = $product->getSpecificLeadTime() ? $product->getSpecificLeadTime() : $leadTime;
        }

        return $leadTime;
    }

    protected function prepareShippingDays($config, $currentDay, $leadTime)
    {
        $deliveryDay = null;

        while ($leadTime) {
            $currentDay->modify('+1 day');

            $isBusinessDay = $this->isBusinessDay($config, $currentDay);

            if(!$isBusinessDay) {
                continue;
            }

            $leadTime--;
            $deliveryDay = $currentDay;
        }

        $nextDeliveryDay = null;
        $dateTime = new \DateTime('now');
        $dateTime->setTimestamp($deliveryDay->getTimestamp());

        while (!$nextDeliveryDay){
            $dateTime->modify('+1 day');

            $isBusinessDay = $this->isBusinessDay($config, $dateTime);

            if(!$isBusinessDay) {
                continue;
            }

            $nextDeliveryDay = $dateTime;
        }

        return ['delivery_day' => $deliveryDay, 'next_delivery_day' => $nextDeliveryDay];
    }

    protected function prepareConfigurationForIndicator($config, $specificDate)
    {
        $config = $this->prepareConfiguration($config);
        $config['timestamp'] = $specificDate ? strtotime($specificDate) : $this->localeDate->scopeTimeStamp();

        return $config;
    }


}