<?php

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Model;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class PopularIconProductsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Model\PopularIconProducts
     */
    protected $popularIconProducts;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration
     */
    protected $configuration;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->popularIconProducts = $this->objectManager->get(\MageSuite\ProductPositiveIndicators\Model\PopularIconProducts::class);
        $this->configuration = $this->objectManager->get(\MageSuite\ProductPositiveIndicators\Helper\Configuration::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadCategories
     * @magentoConfigFixture current_store positive_indicators/popular_icon/active 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 3
     */
    public function testItSetCorrectFlagInProducts()
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
     * @magentoDataFixture loadCategories
     * @magentoConfigFixture current_store positive_indicators/popular_icon/active 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 3
     */
    public function testItReturnsCorrectProductIdsForDefaultSorting()
    {
        $config = $this->configuration->getConfig(\MageSuite\ProductPositiveIndicators\Block\PopularIcon\Product::XML_PATH_CONFIGURATION_KEY);
        $expectedResult = [
            604 => [333],
            603 => [333],
            602 => [333],
            601 => [334]
        ];

        $productIds = $this->popularIconProducts->getProductsData($config);

        $this->assertEquals($expectedResult, $productIds);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadCategories
     * @magentoConfigFixture current_store positive_indicators/popular_icon/active 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/sort_direction asc
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 3
     */
    public function testItReturnsCorrectProductIdsForSpecificSortingDirection()
    {
        $config = $this->configuration->getConfig(\MageSuite\ProductPositiveIndicators\Block\PopularIcon\Product::XML_PATH_CONFIGURATION_KEY);
        $expectedResult = [
            601 => [333,334],
            600 => [333],
            602 => [333]
        ];

        $productIds = $this->popularIconProducts->getProductsData($config);

        $this->assertEquals($expectedResult, $productIds);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadCategories
     * @magentoConfigFixture current_store positive_indicators/popular_icon/active 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/sort_by name
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 3
     */
    public function testItReturnsCorrectProductIdsForSpecificSortBy()
    {
        $config = $this->configuration->getConfig(\MageSuite\ProductPositiveIndicators\Block\PopularIcon\Product::XML_PATH_CONFIGURATION_KEY);
        $expectedResult = [
            604 => [333],
            601 => [333,334],
            603 => [333]
        ];

        $productIds = $this->popularIconProducts->getProductsData($config);

        $this->assertEquals($expectedResult, $productIds);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadCategories
     * @magentoConfigFixture current_store positive_indicators/popular_icon/active 1
     * @magentoConfigFixture current_store positive_indicators/popular_icon/number_of_products 2
     */
    public function testItReturnsCorrectProductIdsForSpecificNumberOfProducts()
    {
        $config = $this->configuration->getConfig(\MageSuite\ProductPositiveIndicators\Block\PopularIcon\Product::XML_PATH_CONFIGURATION_KEY);

        $expectedResult = [
            604 => [333],
            603 => [333],
            601 => [334]
        ];

        $productIds = $this->popularIconProducts->getProductsData($config);

        $this->assertEquals($expectedResult, $productIds);
    }

    public static function loadCategories()
    {
        require __DIR__ . '/../_files/categories_with_products.php';

        $indexerRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Indexer\IndexerRegistry::class);
        $indexerRegistry->get(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID)->reindexAll();
    }

    public static function loadProductsRollback()
    {
        require __DIR__ . '/../_files/categories_with_products_rollback.php';
    }
}