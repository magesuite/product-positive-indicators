<?php

namespace MageSuite\ProductPositiveIndicators\Api;

/**
 * Interface for updating fast shipping order queue
 * @api
 */
interface OrderQueueLengthUpdaterInterface
{
    /**
     * Update order queue value
     *
     * @api
     * @param int $orderQueueLength
     * @return boolean
     */
    public function updateOrderQueueLength($orderQueueLength);
}