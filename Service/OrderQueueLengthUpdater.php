<?php

namespace MageSuite\ProductPositiveIndicators\Service;

class OrderQueueLengthUpdater implements \MageSuite\ProductPositiveIndicators\Api\OrderQueueLengthUpdaterInterface
{
    const XML_PATH_ORDER_QUEUE_LENGTH = 'positive_indicators/fast_shipping/order_queue_length';

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $resourceConfig;

    /**
     * @var \Magento\Framework\App\Cache\Manager
     */
    protected $cacheManager;

    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface  $resourceConfig,
        \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->cacheManager = $cacheManager;
    }

    public function updateOrderQueueLength($orderQueueLength)
    {
        if (!is_numeric($orderQueueLength)) {
            return false;
        }

        $this->resourceConfig->saveConfig(
            self::XML_PATH_ORDER_QUEUE_LENGTH,
            $orderQueueLength,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );

        $this->cacheManager->flush([\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER]);

        return true;
    }
}
