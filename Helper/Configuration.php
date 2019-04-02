<?php

namespace MageSuite\ProductPositiveIndicators\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $config = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfigInterface;
    }

    public function getConfig()
    {
        if($this->config === null){
            $config = $this->getConfigFromDatabase($this->getConfigKey());
            $this->config = new \Magento\Framework\DataObject($config);
        }

        return $this->config;
    }

    public function isEnabled()
    {
        return $this->getConfig()->getIsEnabled();
    }

    protected function getConfigFromDatabase($group)
    {
        return $this->scopeConfig->getValue(sprintf('positive_indicators/%s', $group), \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    protected function convertSecondsToHours($seconds)
    {
        return $seconds * 3600;
    }

    protected function convertStringToArray($string)
    {
        if(empty($string)){
            return [];
        }

        return array_map('trim', explode(',', $string));
    }

    protected function getConfigKey()
    {
        return null;
    }
}
