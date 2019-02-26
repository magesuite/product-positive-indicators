<?php

namespace MageSuite\ProductPositiveIndicators\Service;

class DeliveryDataProvider
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ){
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
    }

    protected function isWorkingDay($config, $currentDay)
    {
        if(in_array($currentDay->format('N'), $config->getWorkingDays())){
            return true;
        }

        return false;
    }

    protected function isHoliday($config, $currentDay)
    {
        if(in_array($currentDay->format('d.m.Y'), $config->getHolidays())){
            return true;
        }

        return false;
    }
}