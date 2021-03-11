<?php

namespace MageSuite\ProductPositiveIndicators\Block\OnlyXAvailable;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_ProductPositiveIndicators::onlyxavailable/product.phtml';

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\OnlyXAvailable
     */
    protected $configuration;

    /**
     * @var \MageSuite\Frontend\Service\Breadcrumb\BreadcrumbCategoryFinderInterface
     */
    protected $categoryFinder;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper,
        \Magento\Framework\Registry $registry,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\OnlyXAvailable $configuration,
        \MageSuite\Frontend\Service\Breadcrumb\BreadcrumbCategoryFinderInterface $categoryFinder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productHelper = $productHelper;
        $this->registry = $registry;
        $this->configuration = $configuration;
        $this->categoryFinder = $categoryFinder;
    }

    public function shouldDisplayInfoOnProductPage($productQty = null)
    {
        if(!$this->configuration->isEnabled()){
            return false;
        }

        $qtyFromConfig = $this->getQuantityFromConfig();

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
        $product = $this->productHelper->getProduct();

        if(!$product){
            return null;
        }

        if($product->getTypeId() != 'simple'){
            return null;
        }

        return $this->productHelper->getProductQty();
    }

    private function getCategory()
    {
        $category = $this->registry->registry('current_category');

        if($category){
            return $category;
        }

        $product = $this->productHelper->getProduct();

        if(!$product){
            return null;
        }

        $category = $this->categoryFinder->getCategory($product);

        return $category ? $category : false;
    }

    private function getQuantityFromConfig()
    {
        $product = $this->productHelper->getProduct();

        if($product and $product->getQtyAvailable() !== null){
            return (float)$product->getQtyAvailable();
        }

        $category = $this->getCategory();

        if($category and $category->getQtyAvailable() !== null){
            return $category->getQtyAvailable();
        }

        return (int)$this->configuration->getQuantity();
    }

}
