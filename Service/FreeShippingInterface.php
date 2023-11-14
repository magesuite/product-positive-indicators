<?php

namespace MageSuite\ProductPositiveIndicators\Service;


interface FreeShippingInterface
{

    /**
     * @param $product
     * @return bool
     */
    public function isFreeShipped($product);

    /**
     * @return array
     */
    public function getShippingMethodsWithFreeShipping();
}
