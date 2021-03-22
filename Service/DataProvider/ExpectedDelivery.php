<?php

namespace MageSuite\ProductPositiveIndicators\Service\DataProvider;

class ExpectedDelivery extends \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProvider implements \MageSuite\ProductPositiveIndicators\Service\DeliveryDataProviderInterface
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery
     */
    protected $configuration;

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

    public function getDeliveryData($product = null)
    {
        if (!$this->isProductInStock($product)) {
            return null;
        }

        $shippingTimeInDays = $this->getShippingTimeInDays($product);
        if (!$shippingTimeInDays) {
            return null;
        }

        $currentDateTime = new \DateTime('now');
        $currentDateTime->setTimestamp($this->configuration->getTimestamp());
        $maxTimeToday = new \DateTime($currentDateTime->format('d.m.Y') . ' ' . $this->configuration->getDeliveryTodayTime());

        $canShipToday = $this->isWorkingDay($currentDateTime) && !$this->isHoliday($currentDateTime);
        $shippingDays = $this->getShippingDays($currentDateTime, $shippingTimeInDays);

        return new \Magento\Framework\DataObject([
            'max_today_time' => $canShipToday ? $maxTimeToday->getTimestamp() : null,
            'ship_day_time' => $shippingDays->getShipDay()->getTimestamp(),
            'ship_day_name' => __($shippingDays->getShipDay()->format('l')),
            'next_ship_day_time' => $shippingDays->getNextShipDay()->getTimestamp(),
            'next_ship_day_name' => __($shippingDays->getNextShipDay()->format('l')),
            'utc_offset' => $this->configuration->getUtcOffset()
        ]);
    }

    protected function getShippingTimeInDays($product)
    {
        $shippingTime = $this->configuration->getDefaultShippingTime();

        if ($product->getUseTimeNeededToShipProduct()) {
            $shippingTime = $product->getTimeNeededToShipProduct() ? $product->getTimeNeededToShipProduct() : $shippingTime;
        }

        return $shippingTime;
    }

    protected function getShippingDays($currentDay, $shippingTimeInDays)
    {
        $shipDay = null;

        while ($shippingTimeInDays) {
            $currentDay->modify('+1 day');

            $isBusinessDay = $this->isWorkingDay($currentDay) && !$this->isHoliday($currentDay);

            if (!$isBusinessDay) {
                continue;
            }

            $shippingTimeInDays--;
            $shipDay = $currentDay;
        }

        $nextShipDay = null;

        $dateTime = new \DateTime('now');
        $dateTime->setTimestamp($shipDay->getTimestamp());

        while (!$nextShipDay) {
            $dateTime->modify('+1 day');

            $isBusinessDay = $this->isWorkingDay($dateTime) && !$this->isHoliday($dateTime);

            if (!$isBusinessDay) {
                continue;
            }

            $nextShipDay = $dateTime;
        }

        return new \Magento\Framework\DataObject([
            'ship_day' => $shipDay,
            'next_ship_day' => $nextShipDay
        ]);
    }

    public function isProductInStock($product)
    {
        return $product->isSalable();
    }
}
