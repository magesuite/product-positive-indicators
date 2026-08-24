<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Service;

interface FreeShippingInterface
{
    public function isFreeShipped(\Magento\Catalog\Api\Data\ProductInterface $product): bool;

    public function getShippingMethodsWithFreeShipping(): array;
}
