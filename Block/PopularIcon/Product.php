<?php

namespace MageSuite\ProductPositiveIndicators\Block\PopularIcon;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_ProductPositiveIndicators::popularicon/product.phtml';

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\PopularIcon
     */
    protected $configuration;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\PopularIcon $configuration,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productHelper = $productHelper;
        $this->configuration = $configuration;
    }

    public function getPopularIconFlag()
    {
        if (!$this->configuration->isEnabled()) {
            return null;
        }

        $product = $this->productHelper->getProduct();

        if (!$product) {
            return null;
        }

        return (boolean)$product->getPopularIcon();
    }

}
