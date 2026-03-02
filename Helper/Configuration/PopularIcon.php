<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Helper\Configuration;

class PopularIcon extends \MageSuite\ProductPositiveIndicators\Helper\Configuration
{
    public const string XML_PATH_CONFIGURATION_KEY = 'popular_icon';

    public function getSortBy(): string
    {
        return $this->getConfig()->getSortBy();
    }

    public function getSortDirection(): string
    {
        return $this->getConfig()->getSortDirection();
    }

    public function getNumberOfProducts(): int
    {
        return (int)$this->getConfig()->getNumberOfProducts();
    }

    public function getMinValue(): ?int
    {
        $value = $this->getConfig()->getMinValue();
        return is_numeric($value) ? (int)$value : null;
    }

    protected function getConfigKey(): string
    {
        return self::XML_PATH_CONFIGURATION_KEY;
    }
}
