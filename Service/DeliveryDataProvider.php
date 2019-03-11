<?php

namespace MageSuite\ProductPositiveIndicators\Service;

class DeliveryDataProvider
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \MageSuite\ProductPositiveIndicators\Helper\Configuration $configuration
    ){
        $this->configuration = $configuration;
    }

    protected function isWorkingDay($currentDay)
    {
        if(in_array($currentDay->format('N'), $this->configuration->getWorkingDays())){
            return true;
        }

        return false;
    }

    protected function isHoliday($currentDay)
    {
        if(in_array($currentDay->format('d.m.Y'), $this->configuration->getHolidays())){
            return true;
        }

        return false;
    }
}