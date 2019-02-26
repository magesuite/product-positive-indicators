<?php

namespace MageSuite\ProductPositiveIndicators\Block\RecentlyBought;

class Product extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_CONFIGURATION_KEY = 'recently_bought';

    protected $_template = 'MageSuite_ProductPositiveIndicators::recentlybought/product.phtml';

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

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
        \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper,
        \Magento\CatalogInventory\Api\StockStateInterface $stockInterface,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration $configuration,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productHelper = $productHelper;
        $this->stockInterface = $stockInterface;
        $this->configuration = $configuration;
    }

    public function getRecentlyBoughtInfo()
    {
        $result = ['active' => 0];

        $config = $this->configuration->getConfig(self::XML_PATH_CONFIGURATION_KEY);

        if(!$config->getActive()){
            return $result;
        }

        $product = $this->productHelper->getProduct();

        if(!$product){
            return $result;
        }

        $recentlyBoughtSum = $product->getRecentlyBoughtSum();

        if(!$recentlyBoughtSum){
            return $result;
        }

        $orderPeriod = $product->getRecentlyBoughtPeriod();
        $orderPeriod = $orderPeriod ? $orderPeriod : $config->getPeriod();

        return [
            'active' => $product->getRecentlyBought(),
            'sum' => $product->getRecentlyBoughtSum(),
            'order_period' => $orderPeriod
        ];
    }
}