<?php

namespace MageSuite\ProductPositiveIndicators\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

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
        parent::__construct($context);

        $this->scopeConfig = $scopeConfigInterface;
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
    }

    public function getConfig($group)
    {
        $config = $this->getConfigFromDatabase($group);

        if(isset($config['working_days'])){
            $config['working_days'] = $this->formatData($config['working_days']);
        }

        if(isset($config['working_hours'])){
            $config['working_hours'] = $this->formatData($config['working_hours'], 'hours');
        }

        if(isset($config['order_queue_length'])){
            $config['order_queue_length'] = $this->formatData($config['order_queue_length'], 'hours');
        }

        if(isset($config['holidays'])){
            $config['holidays'] = !empty($config['holidays']) ? $this->formatData($config['holidays']) : [];
        }

        if(!isset($config['timestamp'])){
            $config['timestamp'] = $this->localeDate->scopeTimeStamp();
        }

        if(!isset($config['utc_offset'])){
            $config['utc_offset'] = $this->dateTime->getGmtOffset();
        }

        return new \Magento\Framework\DataObject($config);
    }

    protected function getConfigFromDatabase($group)
    {
        return $this->scopeConfig->getValue(sprintf('positive_indicators/%s', $group), \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    protected function formatData($data, $type = 'array')
    {
        if($type == 'hours'){
            return $data * 3600;
        }

        $array = explode(',', $data);

        return array_map('trim', $array);
    }
}
