<?php

namespace MageSuite\ProductPositiveIndicators\Model;

class PopularIconProducts
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Action
     */
    protected $productResourceAction;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\PopularIcon
     */
    protected $configuration;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productResourceAction,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\PopularIcon $configuration
    )
    {
        $this->scopeConfig = $scopeConfigInterface;
        $this->productResourceAction = $productResourceAction;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->configuration = $configuration;
    }

    public function execute($test = false)
    {
        $this->removePopularIconFlag();

        if(!$this->configuration->isEnabled()){
            return false;
        }

        $productsData = $this->getProductsData();

        if(empty($productsData)){
            return false;
        }

        $this->addPopularIconFlagToProducts($productsData);
    }

    public function getProductsData()
    {
        $categories = $this->getCategories();

        $productsData = [];

        if(empty($categories)){
            return $productsData;
        }

        foreach($categories as $category){
            if($category->getPopularIcon() === '0'){
                continue;
            }

            $productCollection = $this->getProductCollectionFromCategory($category);

            if(!$productCollection){
                continue;
            }

            $products = $productCollection->getItems();

            foreach($products as $productId => $product){
                $productsData[$productId][] = $category->getId();
            }
        }

        return $productsData;
    }

    private function getProductCollectionFromCategory($category)
    {
        $collection = $this->initializeCollection($category);

        if(!$collection->getSize()){
            return false;
        }

        $collection->setOrder(
            $this->configuration->getSortBy(),
            $this->configuration->getSortDirection()
        );

        $collection->setPage(1, (int)$this->configuration->getNumberOfProducts());

        return $collection;
    }

    private function initializeCollection($category)
    {
        $productCollection = $category->getProductCollection();

        $productCollection->addFieldToFilter('status', 1);
        $productCollection->addCategoryFilter($category);

        return $productCollection;
    }

    protected function addPopularIconFlagToProducts($productIds)
    {
        foreach($productIds AS $productId => $categoryIds){
            $this->productResourceAction->updateAttributes(
                [$productId],
                ['popular_icon' => 1],
                \Magento\Backend\Block\Widget\Grid\Column\Filter\Store::ALL_STORE_VIEWS
            );

            $categories = implode(',', $categoryIds);

            $this->productResourceAction->updateAttributes(
                [$productId],
                ['popular_icon_categories' => $categories],
                \Magento\Backend\Block\Widget\Grid\Column\Filter\Store::ALL_STORE_VIEWS
            );

        }

        return true;
    }

    protected function removePopularIconFlag()
    {
        $products = $this->getProductsWithFlag();

        if(empty($products)){
            return true;
        }

        foreach($products AS $product){
            $this->productResourceAction->updateAttributes(
                [$product->getId()],
                ['popular_icon' => 0],
                \Magento\Backend\Block\Widget\Grid\Column\Filter\Store::ALL_STORE_VIEWS
            );

            $this->productResourceAction->updateAttributes(
                [$product->getId()],
                ['popular_icon_categories' => ''],
                \Magento\Backend\Block\Widget\Grid\Column\Filter\Store::ALL_STORE_VIEWS
            );
        }

        return true;
    }

    private function getProductsWithFlag()
    {
        $collection = $this->productCollectionFactory->create();

        $collection->addAttributeToSelect(['popular_icon']);
        $collection->addFieldToFilter('popular_icon', 1);

        return $collection->getSize() ? $collection->getItems() : [];
    }

    private function getCategories()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('level', ['gt' => 1]);
        $collection->addAttributeToSelect('popular_icon');

        return $collection->getSize() ? $collection->getItems() : [];
    }
}