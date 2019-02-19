<?php

namespace MageSuite\ProductPositiveIndicators\Block\RecentlyBought;

class Product extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_CONFIGURATION_KEY = 'recently_bought';

    protected $_template = 'MageSuite_ProductPositiveIndicators::recentlybought/product.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $stockInterface;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockInterface,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration $configuration,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->stockInterface = $stockInterface;
        $this->configuration = $configuration;
    }

    public function getRecentlyBoughtInfo()
    {
        $result = ['active' => 0];

        $config = $this->configuration->getConfig(self::XML_PATH_CONFIGURATION_KEY);

        if(!$config['active']){
            return $result;
        }

        $product = $this->getProduct();

        if(!$product){
            return $result;
        }

        $recentlyBoughtSum = $product->getRecentlyBoughtSum();

        if(!$recentlyBoughtSum){
            return $result;
        }

        $orderPeriod = $product->getRecentlyBoughtPeriod();
        $orderPeriod = $orderPeriod ? $orderPeriod : $config['period'];

        return [
            'active' => $product->getRecentlyBought(),
            'sum' => $product->getRecentlyBoughtSum(),
            'order_period' => $orderPeriod
        ];
    }

    public function getProduct()
    {
        $product = $this->registry->registry('product');

        if(!$product){
            return false;
        }

        return $product;
    }
}