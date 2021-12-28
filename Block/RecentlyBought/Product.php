<?php

namespace MageSuite\ProductPositiveIndicators\Block\RecentlyBought;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_ProductPositiveIndicators::recentlybought/product.phtml';

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\RecentlyBought
     */
    protected $configuration;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\RecentlyBought $configuration,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productHelper = $productHelper;
        $this->configuration = $configuration;
    }

    public function getRecentlyBoughtInfo()
    {
        $result = ['active' => 0];

        if (!$this->configuration->isEnabled()) {
            return $result;
        }

        $product = $this->productHelper->getProduct();

        if (!$product) {
            return $result;
        }

        $recentlyBoughtSum = $product->getRecentlyBoughtSum();

        if (!$recentlyBoughtSum) {
            return $result;
        }

        $orderPeriod = $product->getRecentlyBoughtPeriod();
        $orderPeriod = $orderPeriod ? $orderPeriod : $this->configuration->getPeriod();

        return [
            'active' => $product->getRecentlyBought(),
            'sum' => $product->getRecentlyBoughtSum(),
            'order_period' => $orderPeriod
        ];
    }
}
