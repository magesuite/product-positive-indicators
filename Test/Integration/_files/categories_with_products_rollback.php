<?php

foreach([600,601,602,603,604] as $productId) {
    $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');

    $product->load($productId);

    if ($product->getId()) {
        $product->delete();
    }
}

foreach([433,434] as $categoryId) {
    $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Category');

    $category->load($productId);

    if ($category->getId()) {
        $category->delete();
    }
}
