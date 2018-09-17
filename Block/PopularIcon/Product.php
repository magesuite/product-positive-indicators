<?php

namespace MageSuite\ProductPositiveIndicators\Block\PopularIcon;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_ProductPositiveIndicators::popularicon/product.phtml';

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

    public function getPopularIconFlag()
    {
        if(!$this->config['active']){
            return false;
        }

        $product = $this->getProduct();

        if(!$product){
            return false;
        }

        return (boolean)$product->getPopularIcon();
    }

    public function getProduct()
    {
        $product = $this->registry->registry('product');

        return $product ? $product : false;
    }

    private function getConfig()
    {
        return $this->scopeConfig->getValue('positive_indicators/popular_icon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}