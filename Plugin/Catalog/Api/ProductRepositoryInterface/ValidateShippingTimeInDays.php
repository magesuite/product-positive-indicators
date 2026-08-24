<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Plugin\Catalog\Api\ProductRepositoryInterface;

class ValidateShippingTimeInDays
{
    public function beforeSave(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $saveOptions = false
    ) {
        if (!$product->getUseTimeNeededToShipProduct()) {
            return [$product, $saveOptions];
        }

        $shippingTime = $product->getTimeNeededToShipProduct();

        if ($shippingTime === null || $shippingTime === '') {
            return [$product, $saveOptions];
        }

        $shippingTimeInDays = filter_var($shippingTime, FILTER_VALIDATE_INT);

        if ($shippingTimeInDays === false) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Specific shipping time must be a whole number of days.')
            );
        }

        if ($shippingTimeInDays < \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery::MIN_SHIPPING_TIME_IN_DAYS
            || $shippingTimeInDays > \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery::MAX_SHIPPING_TIME_IN_DAYS
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Specific shipping time must be between %1 and %2 days.',
                    \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery::MIN_SHIPPING_TIME_IN_DAYS,
                    \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery::MAX_SHIPPING_TIME_IN_DAYS
                )
            );
        }

        return [$product, $saveOptions];
    }
}
