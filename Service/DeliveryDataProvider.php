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

    protected function prepareConfiguration($config)
    {
        $config['working_days'] = $this->prepareDataFromConfiguration($config['working_days']);
        $config['holidays'] = !empty($config['holidays']) ? $this->prepareDataFromConfiguration($config['holidays']) : [];

        $config['utc_offset'] = (int)$this->dateTime->getGmtOffset();

        return $config;
    }

    protected function isBusinessDay($config, $currentDay)
    {
        if(in_array($currentDay->format('N'), $config['working_days']) and !in_array($currentDay->format('d.m.Y'), $config['holidays'])){
            return true;
        }

        return false;
    }

    protected function prepareDataFromConfiguration($data, $type = 'array')
    {
        if($type == 'hours'){
            return $data * 3600;
        }

        $array = explode(',', $data);

        return array_map('trim', $array);
    }
}