<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Test\Integration\Plugin\Catalog\Api\ProductRepositoryInterface;

class ValidateShippingTimeInDaysTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    public function testItRejectsANegativeShippingTimeOnSave(): void
    {
        $product = $this->productRepository->get('custom_product');
        $product->setUseTimeNeededToShipProduct(1);
        $product->setTimeNeededToShipProduct(-1);

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->productRepository->save($product);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    public function testItRejectsAShippingTimeAboveTheSupportedHorizonOnSave(): void
    {
        $product = $this->productRepository->get('custom_product');
        $product->setUseTimeNeededToShipProduct(1);
        $product->setTimeNeededToShipProduct(
            \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery::MAX_SHIPPING_TIME_IN_DAYS + 1
        );

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->productRepository->save($product);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    public function testItRejectsAShippingTimeWithTrailingNonNumericCharactersOnSave(): void
    {
        $product = $this->productRepository->get('custom_product');
        $product->setUseTimeNeededToShipProduct(1);
        $product->setTimeNeededToShipProduct('1foo');

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->productRepository->save($product);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    public function testItRejectsAFractionalShippingTimeOnSave(): void
    {
        $product = $this->productRepository->get('custom_product');
        $product->setUseTimeNeededToShipProduct(1);
        $product->setTimeNeededToShipProduct('365.9');

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->productRepository->save($product);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ProductPositiveIndicators::Test/Integration/_files/expected_delivery_products.php
     */
    public function testItAllowsAValidShippingTimeOnSave(): void
    {
        $product = $this->productRepository->get('custom_product');
        $product->setUseTimeNeededToShipProduct(1);
        $product->setTimeNeededToShipProduct(4);

        $saved = $this->productRepository->save($product);

        $this->assertEquals(4, $saved->getTimeNeededToShipProduct());
    }
}
