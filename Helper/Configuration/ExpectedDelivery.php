<?php

namespace MageSuite\ProductPositiveIndicators\Helper\Configuration;

class ExpectedDelivery extends \MageSuite\ProductPositiveIndicators\Helper\Configuration
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
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        parent::__construct($context, $scopeConfigInterface);

        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
    }

    public function getConfig($group)
    {
        $config = parent::getConfig($group);

        $config->setWorkingDays($this->formatData($config['working_days']));
        $config->setHolidays($this->formatData($config['holidays']));

        if(!$config->getTimestamp()){
            $config->setTimestamp($this->localeDate->scopeTimeStamp());
        }

        if(!$config->getUtcOffset()){
            $config->setUtcOffset($this->dateTime->getGmtOffset());
        }

        return $config;
    }
}
