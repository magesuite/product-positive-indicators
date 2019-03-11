<?php

namespace MageSuite\ProductPositiveIndicators\Service\DataProvider;

class ExpectedDelivery extends \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery
     */
    protected $configuration;

    public function __construct(
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery $configuration
    ){
        parent::__construct($configuration);
    }

    public function getDeliveryData($product = null)
    {
        $isInStock = $product->getQuantityAndStockStatus()['is_in_stock'];

        if(empty($isInStock)){
            return null;
        }

        $shippingTimeInDays = $this->getShippingTimeInDays($product);

        if(!$shippingTimeInDays){
            return null;
        }

        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp($this->configuration->getTimestamp());
        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $this->configuration->getDeliveryTodayTime());

        $canShipToday = $this->isWorkingDay($currentDateTime) && !$this->isHoliday($currentDateTime);
        $shippingDays = $this->getShippingDays($currentDateTime, $shippingTimeInDays);

        return [
            'maxTodayTime' => $canShipToday ? $maxTimeToday->getTimestamp() : null,
            'deliveryDayTime' => $shippingDays['delivery_day']->getTimestamp(),
            'deliveryDayName' => __($shippingDays['delivery_day']->format('l')),
            'deliveryNextDayTime' => $shippingDays['next_delivery_day']->getTimestamp(),
            'deliveryNextDayName' => __($shippingDays['next_delivery_day']->format('l')),
            'utcOffset' => $this->configuration->getUtcOffset()
        ];

    }

    protected function getShippingTimeInDays($product)
    {
        $shippingTime = $this->configuration->getDefaultShippingTime();

        if($product->getUseTimeNeededToShipProduct()){
            $shippingTime = $product->getTimeNeededToShipProduct() ? $product->getTimeNeededToShipProduct() : $shippingTime;
        }

        return $shippingTime;
    }

    protected function getShippingDays($currentDay, $shippingTimeInDays)
    {
        $deliveryDay = null;

        while ($shippingTimeInDays) {
            $currentDay->modify('+1 day');

            $isBusinessDay = $this->isWorkingDay($currentDay) && !$this->isHoliday($currentDay);

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

            $isBusinessDay = $this->isWorkingDay($dateTime) && !$this->isHoliday($dateTime);

            if(!$isBusinessDay) {
                continue;
            }

            $nextDeliveryDay = $dateTime;
        }

        return ['delivery_day' => $deliveryDay, 'next_delivery_day' => $nextDeliveryDay];
    }


}