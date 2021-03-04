<?php

namespace MageSuite\ProductPositiveIndicators\Model;

class PopularIconProducts
{
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
        \Magento\Catalog\Model\ResourceModel\Product\Action $productResourceAction,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\PopularIcon $configuration
    ) {
        $this->productResourceAction = $productResourceAction;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->configuration = $configuration;
    }

    public function execute($test = false)
    {
        if (!$this->configuration->isEnabled()) {
            return false;
        }

        $this->removePopularIconFlag();
        $productsData = $this->getProductsData();

        if (empty($productsData)){
            return false;
        }

        $this->addPopularIconFlagToProducts($productsData);
    }

    public function getProductsData()
    {
        $categories = $this->getCategories();
        $productsData = [];

        if (!$categories->count()) {
            return $productsData;
        }

        foreach ($categories as $category) {
            $productCollection = $this->getProductCollectionFromCategory($category);

            if (!$productCollection->count()) {
                continue;
            }

            foreach($productCollection->getItems() as $productId => $product){
                $productsData[$productId][] = $category->getId();
            }
        }

        return $productsData;
    }

    protected function getProductCollectionFromCategory($category)
    {
        $numberOfProducts = (int)$this->configuration->getNumberOfProducts();
        $collection = $this->initializeCollection($category);
        $collection->setOrder(
            $this->configuration->getSortBy(),
            $this->configuration->getSortDirection()
        );
        $collection->setPage(1, $numberOfProducts);

        return $collection;
    }

    protected function initializeCollection($category)
    {
        /** @see \Magento\Catalog\Model\ResourceModel\Product\Collection::addCategoryFilter */
        $category->setIsAnchor(1);
        $productCollection = $category->getProductCollection();
        $productCollection->addAttributeToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE]);
        $productCollection->addAttributeToFilter('status', 1);

        return $productCollection;
    }

    protected function addPopularIconFlagToProducts($productIds)
    {
        $this->productResourceAction->updateAttributes(
            array_keys($productIds),
            ['popular_icon' => 1],
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        foreach($productIds AS $productId => $categoryIds) {
            $categories = implode(',', $categoryIds);
            $this->productResourceAction->updateAttributes(
                [$productId],
                ['popular_icon_categories' => $categories],
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );

        }

        return true;
    }

    protected function removePopularIconFlag()
    {
        $products = $this->getProductsWithFlag();

        if (!$products->count()) {
            return true;
        }

        foreach ($products AS $product) {
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

    protected function getProductsWithFlag()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('popular_icon');
        $collection->addAttributeToFilter('popular_icon', 1);

        return $collection;
    }

    protected function getCategories()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('level', ['gt' => 1])
            ->addAttributeToFilter([
                ['attribute' => 'popular_icon', 'null' => true],
                ['attribute' => 'popular_icon', 'eq' => 1],
            ])
            ->addAttributeToSelect('popular_icon');

        return $collection;
    }
}
