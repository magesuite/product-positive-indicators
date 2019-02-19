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
        return $this->scopeConfig->getValue('positive_indicators/' . $group, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
