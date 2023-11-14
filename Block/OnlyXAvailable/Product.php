<?php

namespace MageSuite\ProductPositiveIndicators\Block\OnlyXAvailable;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_ProductPositiveIndicators::onlyxavailable/product.phtml';

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

    /**
     * @var \MageSuite\Frontend\Service\Breadcrumb\BreadcrumbCategoryFinderInterface
     */
    protected $categoryFinder;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\CatalogInventory\Api\StockStateInterface $stockInterface,
        \MageSuite\Frontend\Service\Breadcrumb\BreadcrumbCategoryFinderInterface $categoryFinder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->scopeConfig = $scopeConfigInterface;
        $this->stockInterface = $stockInterface;
        $this->categoryFinder = $categoryFinder;

        $this->config = $this->getConfig();
    }

    public function displayInfoOnProductPage($productQty = null)
    {
        if(!$this->config['active']){
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
        $product = $this->getProduct();

        if(!$product){
            return false;
        }

        if($product->getTypeId() != 'simple'){
            return false;
        }

        $qty = $product->getExtensionAttributes()->getStockItem()->getQty();
        $qty = $qty ? $qty : $this->stockInterface->getStockQty($product->getId());

        return $qty;
    }

    public function getProduct()
    {
        $product = $this->registry->registry('product');

        return $product ? $product : false;
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

    private function getQuantityFromConfig()
    {
        $product = $this->getProduct();

        if($product and $product->getQtyAvailable() !== null){
            return (float)$product->getQtyAvailable();
        }

        $category = $this->getCategory();

        if($category and $category->getQtyAvailable() !== null){
            return $category->getQtyAvailable();
        }

        return (int)$this->config['quantity'];
    }

    private function getConfig()
    {
        return $this->scopeConfig->getValue('positive_indicators/only_x_available', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}
