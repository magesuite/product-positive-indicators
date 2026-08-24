<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Block\FreeShipping;

class Product extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'MageSuite_ProductPositiveIndicators::freeshipping/product.phtml'; // phpcs:ignore

    protected \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper;

    protected \MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface $freeShippingService;

    protected \MageSuite\ProductPositiveIndicators\Helper\Configuration\FreeShipping $freeShippingConfiguration;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper,
        \MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface $freeShippingService,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\FreeShipping $freeShippingConfiguration,
        array $data = []
    ) {
        $this->productHelper = $productHelper;
        $this->freeShippingService = $freeShippingService;
        $this->freeShippingConfiguration = $freeShippingConfiguration;
        parent::__construct($context, $data);
    }

    public function isFreeShippingAvailable(): bool
    {
        $product = $this->productHelper->getProduct();

        if (!$product) {
            return false;
        }

        return $this->freeShippingService->isFreeShipped($product);
    }

    public function showTextNoteOnProductsDetailpage(): bool
    {
        return $this->freeShippingConfiguration->showTextNoteOnProductsDetailpage();
    }

    public function showBadgeOnProductsDetailpage(): bool
    {
        return $this->freeShippingConfiguration->showBadgeOnProductsDetailpage();
    }
}
