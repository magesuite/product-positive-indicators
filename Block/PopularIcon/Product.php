<?php

namespace MageSuite\ProductPositiveIndicators\Block\PopularIcon;

class Product extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_CONFIGURATION_KEY = 'popular_icon';

    protected $_template = 'MageSuite_ProductPositiveIndicators::popularicon/product.phtml';

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

    public function getPopularIconFlag()
    {
        $config = $this->configuration->getConfig(self::XML_PATH_CONFIGURATION_KEY);

        if(!$config['active']){
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

        if(!$product){
            return false;
        }

        return $product;
    }

}