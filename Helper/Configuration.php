<?php

namespace MageSuite\ProductPositiveIndicators\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfigInterface;
    }

    public function getConfig($group)
    {
        $config = $this->getConfigFromDatabase($group);

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

        if(empty($data)){
            return [];
        }

        $array = explode(',', $data);

        return array_map('trim', $array);
    }
}
