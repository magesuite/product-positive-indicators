<?php

namespace MageSuite\ProductPositiveIndicators\Helper\Configuration;

class RecentlyBought extends \MageSuite\ProductPositiveIndicators\Helper\Configuration
{
    const XML_PATH_CONFIGURATION_KEY = 'recently_bought';

    public function getPeriod()
    {
        return $this->getConfig()->getPeriod();
    }

    public function getMinimal()
    {
        return $this->getConfig()->getMinimal();
    }

    protected function getConfigKey()
    {
        return self::XML_PATH_CONFIGURATION_KEY;
    }
}
