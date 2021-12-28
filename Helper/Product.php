<?php

namespace MageSuite\ProductPositiveIndicators\Helper;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface
     */
    protected $freeShippingService;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\PopularIcon
     */
    protected $popularIconConfiguration;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping
     */
    protected $fastShippingConfiguration;

    /**
     * @var \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface
     */
    protected $getProductSalableQty;

    /**
     * @var \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite
     */
    protected $getStockIdForCurrentWebsite;

    /**
     * @var array
     */
    protected $productQtyCache = [];

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface $freeShippingService,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\PopularIcon $popularIconConfiguration,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping $fastShippingConfiguration,
        \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getProductSalableQty,
        \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
    ) {
        parent::__construct($context);

        $this->productResource = $productResource;
        $this->storeManager = $storeManager;
        $this->freeShippingService = $freeShippingService;
        $this->registry = $registry;
        $this->productRepository = $productRepository;
        $this->fastShippingConfiguration = $fastShippingConfiguration;
        $this->popularIconConfiguration = $popularIconConfiguration;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
    }

    public function getPopularIconFlag($product)
    {
        if (!$this->popularIconConfiguration->isEnabled()) {
            return false;
        }

        $product = $this->getProduct($product);

        if (!$product) {
            return false;
        }

        $isPopularIconEnabled = $this->isPopularIconEnabled($product);

        if (!$isPopularIconEnabled) {
            return $isPopularIconEnabled;
        }

        $currentProduct = $this->registry->registry('product');
        $currentCategory = $this->registry->registry('current_category');

        if (!$currentCategory or $currentProduct) {
            return $isPopularIconEnabled;
        }

        $enabledInCategory = $this->isEnabledInSpecificCategory($product, $currentCategory->getId());

        return $enabledInCategory;
    }

    public function getProduct($product = null)
    {
        if (!$product) {
            $product = $this->registry->registry('product');
            return $product ? $product : null;
        }

        if ($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            return $product;
        }

        if (!is_int($product) and !is_string($product)) {
            return null;
        }

        try {
            $product = $this->productRepository->getById($product);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }

        return $product;
    }

    public function getProductQty($product = null)
    {
        $product = $product ?? $this->getProduct();

        if (!$product) {
            return 0;
        }

        if (!isset($this->productQtyCache[$product->getId()])) {
            $stockId = $this->getStockIdForCurrentWebsite->execute();
            try {
                $qty = $this->getProductSalableQty->execute($product->getSku(), $stockId);
            } catch (\Magento\Framework\Exception\InputException $e) {
                $qty = 0;
            }
            $this->productQtyCache[$product->getId()] = $qty;
        }

        return $this->productQtyCache[$product->getId()];
    }

    private function isPopularIconEnabled($product)
    {
        return (boolean)$product->getPopularIcon();
    }

    private function isEnabledInSpecificCategory($product, $categoryId)
    {
        $enabledInCategories = $product->getPopularIconCategories();

        if (!$enabledInCategories) {
            return false;
        }

        $categories = explode(',', $enabledInCategories);

        return in_array($categoryId, $categories) ? true : false;
    }

    public function isFastShippingEnabled()
    {
        return (boolean)$this->fastShippingConfiguration->isEnabled();
    }

    public function isFreeShipped($product)
    {
        return $this->freeShippingService->isFreeShipped($product);
    }

    public function showFreeShippingInProductTiles()
    {
        return $this->freeShippingService->showInProductTiles();
    }

    public function showFreeShippingTextNoteOnProductsDetailpage()
    {
        return $this->freeShippingService->showTextNoteOnProductsDetailpage();
    }

    public function showFreeShippingBadgeOnProductsDetailpage()
    {
        return $this->freeShippingService->showBadgeOnProductsDetailpage();
    }

    public function showFreeShippingInSearchAutosuggest()
    {
        return $this->freeShippingService->showInSearchAutosuggest();
    }
}
