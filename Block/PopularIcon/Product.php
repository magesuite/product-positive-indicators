<?php

namespace MageSuite\ProductPositiveIndicators\Block\PopularIcon;

class Product extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_CONFIGURATION_KEY = 'popular_icon';

    protected $_template = 'MageSuite_ProductPositiveIndicators::popularicon/product.phtml';

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

    public function getPopularIconFlag()
    {
        $config = $this->configuration->getConfig(self::XML_PATH_CONFIGURATION_KEY);

        if(!$config->getActive()){
            return null;
        }

        $product = $this->productHelper->getProduct();

        if(!$product){
            return null;
        }

        return (boolean)$product->getPopularIcon();
    }

}