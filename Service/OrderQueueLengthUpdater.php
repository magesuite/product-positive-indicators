<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Service;

class OrderQueueLengthUpdater implements \MageSuite\ProductPositiveIndicators\Api\OrderQueueLengthUpdaterInterface
{
    protected const XML_PATH_ORDER_QUEUE_LENGTH = 'positive_indicators/fast_shipping/order_queue_length';

    public const MAX_ORDER_QUEUE_LENGTH_HOURS = 8760;

    protected \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig;

    protected \Magento\Framework\App\Cache\Manager $cacheManager;

    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig,
        \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->cacheManager = $cacheManager;
    }

    public function updateOrderQueueLength(mixed $orderQueueLength): bool
    {
        if (!$this->isValidOrderQueueLength($orderQueueLength)) {
            return false;
        }

        $this->resourceConfig->saveConfig(
            self::XML_PATH_ORDER_QUEUE_LENGTH,
            (int)$orderQueueLength,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );

        $this->cacheManager->flush([\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER]);

        return true;
    }

    protected function isValidOrderQueueLength(mixed $orderQueueLength): bool
    {
        if (is_int($orderQueueLength)) {
            $orderQueueLength = (string)$orderQueueLength;
        }

        if (!is_string($orderQueueLength) || !preg_match('/^\d+$/', $orderQueueLength)) {
            return false;
        }

        return (int)$orderQueueLength <= self::MAX_ORDER_QUEUE_LENGTH_HOURS;
    }
}
