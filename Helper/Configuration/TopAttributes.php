<?php

namespace MageSuite\ProductPositiveIndicators\Helper\Configuration;

class TopAttributes extends \MageSuite\ProductPositiveIndicators\Helper\Configuration
{
    const XML_PATH_CONFIGURATION_KEY = 'top_attributes';

    protected function getConfigKey()
    {
        return self::XML_PATH_CONFIGURATION_KEY;
    }
}
