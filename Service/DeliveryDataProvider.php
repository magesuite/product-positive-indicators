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

    protected function getConfiguration($config)
    {
        $config['working_days'] = $this->getDataFromConfiguration($config['working_days']);
        $config['holidays'] = !empty($config['holidays']) ? $this->getDataFromConfiguration($config['holidays']) : [];

        $config['utc_offset'] = (int)$this->dateTime->getGmtOffset();

        return $config;
    }

    protected function isWorkingDay($config, $currentDay)
    {
        if(in_array($currentDay->format('N'), $config['working_days'])){
            return true;
        }

        return false;
    }

    protected function isNotHoliday($config, $currentDay)
    {
        if(in_array($currentDay->format('N'), $config['working_days']) and !in_array($currentDay->format('d.m.Y'), $config['holidays'])){
            return true;
        }

        return false;
    }

    protected function getDataFromConfiguration($data, $type = 'array')
    {
        if($type == 'hours'){
            return $data * 3600;
        }

        $array = explode(',', $data);

        return array_map('trim', $array);
    }
}