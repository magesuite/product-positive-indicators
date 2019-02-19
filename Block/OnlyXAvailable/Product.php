<?php

namespace MageSuite\ProductPositiveIndicators\Block\OnlyXAvailable;

class Product extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_CONFIGURATION_KEY = 'only_x_available';

    protected $_template = 'MageSuite_ProductPositiveIndicators::onlyxavailable/product.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface
     */
    protected $getProductSalableQty;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\Frontend\Service\Breadcrumb\BreadcrumbCategoryFinderInterface
     */
    protected $categoryFinder;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getProductSalableQty,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration $configuration,
        \MageSuite\Frontend\Service\Breadcrumb\BreadcrumbCategoryFinderInterface $categoryFinder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->configuration = $configuration;
        $this->categoryFinder = $categoryFinder;
    }

    public function displayInfoOnProductPage($productQty = null)
    {
        $config = $this->configuration->getConfig(self::XML_PATH_CONFIGURATION_KEY);

        if(!$config['active']){
            return false;
        }

        $qtyFromConfig = $this->getQuantityFromConfig($config);

        if(!$qtyFromConfig){
            return false;
        }

        $productQty = $productQty ? $productQty : $this->getProductQty();

        if(!$productQty or (int)$productQty < 0){
            return false;
        }

        return $productQty < $qtyFromConfig ? true : false;
    }

    public function getProductQty()
    {
        $product = $this->getProduct();

        if(!$product){
            return false;
        }

        if($product->getTypeId() != 'simple'){
            return false;
        }

        $stockId = $product->getExtensionAttributes()->getStockItem()->getStockId();

        return $this->getProductSalableQty->execute($product->getSku(), $stockId);
    }

    public function getProduct()
    {
        $product = $this->registry->registry('product');

        if(!$product){
            return false;
        }

        return $product;
    }

    private function getCategory()
    {
        $category = $this->registry->registry('current_category');

        if($category){
            return $category;
        }

        $product = $this->getProduct();

        if(!$product){
            return false;
        }

        $category = $this->categoryFinder->getCategory($product);

        return $category ? $category : false;
    }

    private function getQuantityFromConfig($config)
    {
        $product = $this->getProduct();

        if($product and $product->getQtyAvailable() !== null){
            return (float)$product->getQtyAvailable();
        }

        $category = $this->getCategory();

        if($category and $category->getQtyAvailable() !== null){
            return $category->getQtyAvailable();
        }

        return (int)$config['quantity'];
    }

}