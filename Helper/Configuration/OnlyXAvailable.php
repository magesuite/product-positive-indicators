<?php

namespace MageSuite\ProductPositiveIndicators\Helper\Configuration;

class OnlyXAvailable extends \MageSuite\ProductPositiveIndicators\Helper\Configuration
{
    const XML_PATH_CONFIGURATION_KEY = 'only_x_available';

    public function getQuantity()
    {
        return $this->getConfig()->getQuantity();
    }

    protected function getConfigKey()
    {
        return self::XML_PATH_CONFIGURATION_KEY;
    }
}
