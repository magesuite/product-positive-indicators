<?php


require __DIR__ . '/../_files/products.php';
require __DIR__ . '/../_files/orders_mapper.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(605)
    ->setAttributeSetId(4)
    ->setName('Product with different period and minimal')
    ->setSku('product_different_two_attr')
    ->setUrlKey('product_different_two_attr')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setRecentlyBoughtPeriod(10)
    ->setRecentlyBoughtMinimal(50)
    ->save();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(606)
    ->setAttributeSetId(4)
    ->setName('Product with short period and small minimal')
    ->setSku('product_with_short_period')
    ->setUrlKey('product_with_short_period')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setRecentlyBoughtPeriod(3)
    ->setRecentlyBoughtMinimal(10)
    ->save();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(607)
    ->setAttributeSetId(4)
    ->setName('Product with custom minimal')
    ->setSku('product_custom_minimal')
    ->setUrlKey('product_custom_minimal')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setRecentlyBoughtMinimal(10)
    ->save();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(608)
    ->setAttributeSetId(4)
    ->setName('Product with custom period')
    ->setSku('product_custom_period')
    ->setUrlKey('product_custom_period')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 10, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setRecentlyBoughtPeriod(10)
    ->save();

$addressData = [
    'region' => 'CA',
    'postcode' => '11111',
    'lastname' => 'lastname',
    'firstname' => 'firstname',
    'street' => 'street',
    'city' => 'Los Angeles',
    'email' => 'admin@example.com',
    'telephone' => '11111111',
    'country_id' => 'US'
];

foreach ($orderMapper as $incrementId => $orderData) {
    $billingAddress = $objectManager->create(
        'Magento\Sales\Model\Order\Address',
        ['data' => $addressData]
    );

    $billingAddress->setAddressType('billing');

    $shippingAddress = clone $billingAddress;
    $shippingAddress->setId(null)->setAddressType('shipping');

    $payment = $objectManager->create(Magento\Sales\Model\Order\Payment::class);
    $payment->setMethod('checkmo')
        ->setAdditionalInformation([
            'token_metadata' => [
                'token' => 'f34vjw',
                'customer_id' => 1
            ]
        ]);

    $orderItem = $objectManager->create('Magento\Sales\Model\Order\Item');
    $orderItem->setProductId($orderData['product_id'])->setQtyOrdered($orderData['qty_ordered']);
    $orderItem->setBasePrice($orderData['product_price']);
    $orderItem->setPrice($orderData['product_price']);
    $orderItem->setRowTotal($orderData['product_price']);
    $orderItem->setProductType('simple');
    $orderItem->setSku('simple-'.$orderData['product_id']);
    $orderItem->setCreatedAt($orderData['date']);


    $order = $objectManager->create('Magento\Sales\Model\Order');

    $order->loadByIncrementId($incrementId);
    if ($order->getId()) {
        continue;
    }
    $order->setIncrementId(
        $incrementId
    )->setState(
        \Magento\Sales\Model\Order::STATE_PROCESSING
    )->setCreatedAt(
        $orderData['date']
    )->setStatus(
        $order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
    )->setSubtotal(
        $orderData['product_price'] * $orderData['qty_ordered']
    )->setGrandTotal(
        $orderData['product_price'] * $orderData['qty_ordered']
    )->setBaseSubtotal(
        $orderData['product_price'] * $orderData['qty_ordered']
    )->setBaseGrandTotal(
        $orderData['product_price'] * $orderData['qty_ordered']
    )->setCustomerIsGuest(
        true
    )->setCustomerEmail(
        'customer@null.com'
    )->setBillingAddress(
        $billingAddress
    )->setShippingAddress(
        $shippingAddress
    )->setStoreId(
        $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()
    )->addItem(
        $orderItem
    )->setPayment(
        $payment
    );
    $order->save();
}

