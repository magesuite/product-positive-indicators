<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Model;

class PopularIconProducts
{
    public function __construct(
        protected \Magento\Catalog\Model\ResourceModel\Product\Action $productResourceAction,
        protected \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        protected \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        protected \MageSuite\ProductPositiveIndicators\Helper\Configuration\PopularIcon $configuration
    ) {
    }

    public function execute(): bool
    {
        if (!$this->configuration->isEnabled()) {
            return false;
        }

        $this->removePopularIconFlag();
        $productsData = $this->getProductsData();

        if (empty($productsData)) {
            return false;
        }

        $this->addPopularIconFlagToProducts($productsData);

        return true;
    }

    public function getProductsData(): array
    {
        $categories = $this->getCategories();
        $productsData = [];

        if (!$categories->count()) {
            return $productsData;
        }

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($categories as $category) {
            $productCollection = $this->getProductCollectionFromCategory($category);

            if (!$productCollection->count()) {
                continue;
            }

            foreach ($productCollection->getItems() as $productId => $product) {
                $productsData[$productId][] = $category->getId();
            }
        }

        return $productsData;
    }

    protected function getProductCollectionFromCategory(
        \Magento\Catalog\Api\Data\CategoryInterface $category
    ): \Magento\Catalog\Model\ResourceModel\Product\Collection {
        $numberOfProducts = (int)$this->configuration->getNumberOfProducts();
        $threshold = $this->configuration->getMinValue();
        $sortBy = $this->configuration->getSortBy();

        $collection = $this->initializeCollection($category);

        if ($threshold !== null) {
            $collection->addAttributeToFilter($sortBy, ['gteq' => $threshold]);
        }

        $collection->setOrder($sortBy, $this->configuration->getSortDirection());
        $collection->setPage(1, $numberOfProducts);

        return $collection;
    }

    protected function initializeCollection(
        \Magento\Catalog\Api\Data\CategoryInterface $category
    ): \Magento\Catalog\Model\ResourceModel\Product\Collection {
        /** @see \Magento\Catalog\Model\ResourceModel\Product\Collection::addCategoryFilter */
        $category->setIsAnchor(1);
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $category->getProductCollection();
        $productCollection->addAttributeToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE]);
        $productCollection->addAttributeToFilter('status', 1);

        return $productCollection;
    }

    protected function addPopularIconFlagToProducts(array $productIds): bool
    {
        $this->productResourceAction->updateAttributes(
            array_keys($productIds),
            ['popular_icon' => 1],
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        foreach ($productIds as $productId => $categoryIds) {
            $categories = implode(',', $categoryIds);
            $this->productResourceAction->updateAttributes(
                [$productId],
                ['popular_icon_categories' => $categories],
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );
        }

        return true;
    }

    protected function removePopularIconFlag(): bool
    {
        $products = $this->getProductsWithFlag();

        if (!$products->count()) {
            return true;
        }

        foreach ($products as $product) {
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

    protected function getProductsWithFlag(): \Magento\Catalog\Model\ResourceModel\Product\Collection
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('popular_icon');
        $collection->addAttributeToFilter('popular_icon', 1);

        return $collection;
    }

    protected function getCategories(): \Magento\Catalog\Model\ResourceModel\Category\Collection
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
