<?php

namespace MageSuite\ProductPositiveIndicators\Service;

interface DeliveryDataProviderInterface
{
    /**
     * Get working days
     *
     * @param [] $config
     * @return array
     */
    public function prepareDeliveryData($config);
}
