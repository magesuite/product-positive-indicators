<?php

namespace MageSuite\ProductPositiveIndicators\Helper\Configuration;

class ExpectedDelivery extends \MageSuite\ProductPositiveIndicators\Helper\Configuration
{
    const XML_PATH_CONFIGURATION_KEY = 'expected_delivery';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        parent::__construct($context, $scopeConfigInterface);

        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
    }

    public function getDeliveryTodayTime()
    {
        return $this->getConfig()->getDeliveryTodayTime();
    }

    public function getWorkingDays()
    {
        $workingDays = $this->getConfig()->getWorkingDays();
        return $this->formatData($workingDays);
    }

    public function getHolidays()
    {
        $holidays = $this->getConfig()->getHolidays();
        return $this->formatData($holidays);
    }

    public function getDefaultShippingTime()
    {
        $defaultShippingTime = $this->getConfig()->getDefaultShippingTime();
        return $defaultShippingTime ?? 0;
    }

    public function getTimestamp()
    {
        $timestamp = $this->getConfig()->getTimestamp();
        return $timestamp ? $timestamp : $this->localeDate->scopeTimeStamp();
    }

    public function getUtcOffset()
    {
        $utcOffset = $this->getConfig()->getUtcOffset();
        return $utcOffset !== null ? $utcOffset : $this->dateTime->getGmtOffset();
    }

    protected function getConfigKey()
    {
        return self::XML_PATH_CONFIGURATION_KEY;
    }
}
