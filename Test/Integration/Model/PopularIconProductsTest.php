<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Model;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class PopularIconProductsTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager = null;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository = null;
    protected ?\MageSuite\ProductPositiveIndicators\Model\PopularIconProducts $popularIconProducts = null;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->popularIconProducts = $this->objectManager->get(\MageSuite\ProductPositiveIndicators\Model\PopularIconProducts::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/categories_with_products.php
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 3
     */
    public function testItSetCorrectFlagInProducts(): void
    {
        $this->popularIconProducts->execute();

        $productPrice10 = $this->productRepository->get('product_price_10');
        $this->assertNotTrue($productPrice10->getPopularIcon());

        $productPrice20 = $this->productRepository->get('product_price_20');
        $this->assertEquals(1, $productPrice20->getPopularIcon());

        $productPrice50 = $this->productRepository->get('product_price_50');
        $this->assertEquals(1, $productPrice50->getPopularIcon());
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/categories_with_products.php
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 3
     */
    public function testItReturnsCorrectProductIdsForDefaultSorting(): void
    {
        $expectedResult = [
            604 => [433],
            603 => [433],
            602 => [433],
            601 => [434]
        ];

        $productIds = $this->popularIconProducts->getProductsData();

        $this->assertEquals($expectedResult, $productIds);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/categories_with_products.php
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/sort_direction asc
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 3
     */
    public function testItReturnsCorrectProductIdsForSpecificSortingDirection(): void
    {
        $expectedResult = [
            601 => [433,434],
            600 => [433],
            602 => [433]
        ];

        $productIds = $this->popularIconProducts->getProductsData();

        $this->assertEquals($expectedResult, $productIds);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/categories_with_products.php
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/sort_by name
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 3
     */
    public function testItReturnsCorrectProductIdsForSpecificSortBy(): void
    {
        $expectedResult = [
            604 => [433],
            601 => [433,434],
            603 => [433]
        ];

        $productIds = $this->popularIconProducts->getProductsData();

        $this->assertEquals($expectedResult, $productIds);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/categories_with_products.php
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 2
     */
    public function testItReturnsCorrectProductIdsForSpecificNumberOfProducts(): void
    {
        $expectedResult = [
            604 => [433],
            603 => [433],
            601 => [434]
        ];

        $productIds = $this->popularIconProducts->getProductsData();

        $this->assertEquals($expectedResult, $productIds);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/categories_with_products.php
     * @magentoConfigFixture current_store positive_indicators/popular_icon/is_enabled 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 3
     * @magentoConfigFixture current_store positive_indicators/popular_icon/min_value 40
     */
    public function testItSetCorrectFlagInProductsWithThreshold(): void
    {
        $this->popularIconProducts->execute();

        $productPrice10 = $this->productRepository->get('product_price_10');
        $this->assertFalse((bool)$productPrice10->getPopularIcon());

        $productPrice20 = $this->productRepository->get('product_price_20');
        $this->assertFalse((bool)$productPrice20->getPopularIcon());

        $productPrice40 = $this->productRepository->get('product_price_40');
        $this->assertTrue((bool)$productPrice40->getPopularIcon());

        $productPrice50 = $this->productRepository->get('product_price_50');
        $this->assertTrue((bool)$productPrice50->getPopularIcon());
    }
}
