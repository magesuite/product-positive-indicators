<?php

namespace MageSuite\ProductPositiveIndicators\Helper\Configuration;

class PopularIcon extends \MageSuite\ProductPositiveIndicators\Helper\Configuration
{
    const XML_PATH_CONFIGURATION_KEY = 'popular_icon';

    public function getSortBy()
    {
        return $this->getConfig()->getSortBy();
    }

    public function getSortDirection()
    {
        return $this->getConfig()->getSortDirection();
    }

    public function getNumberOfProducts()
    {
        return $this->getConfig()->getNumberOfProducts();
    }

    protected function getConfigKey()
    {
        return self::XML_PATH_CONFIGURATION_KEY;
    }
}
