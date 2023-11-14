<?php

namespace MageSuite\ProductPositiveIndicators\Block\RecentlyBought;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_ProductPositiveIndicators::recentlybought/product.phtml';

    private $config;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $stockInterface;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\CatalogInventory\Api\StockStateInterface $stockInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->scopeConfig = $scopeConfigInterface;
        $this->stockInterface = $stockInterface;

        $this->config = $this->getConfig();
    }

    public function getRecentlyBoughtInfo()
    {
        $result = ['active' => 0];

        if(!$this->config['active']){
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

        return [
            'active' => $product->getRecentlyBought(),
            'sum' => $product->getRecentlyBoughtSum(),
            'order_period' => $this->getOrderPeriodForProduct($product->getRecentlyBoughtPeriod())
        ];
    }

    public function getProduct()
    {
        $product = $this->registry->registry('product');

        return $product ? $product : false;
    }

    private function getOrderPeriodForProduct($period)
    {
        return $period ? $period : $this->config['period'];
    }

    private function getConfig()
    {
        return $this->scopeConfig->getValue('positive_indicators/recently_bought', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}
