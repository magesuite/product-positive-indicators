<?php

$registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

foreach ([600,601,602,603,604,605,606,607,608] as $productId) {
    $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');

    $product->load($productId);

    if ($product->getId()) {
        $product->delete();
    }
}

foreach (range(10000001, 10000013) as $incrementId) {
    $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');

    $order->loadByIncrementId($incrementId);

    if ($order->getId()) {
        $order->delete();
    }
}
