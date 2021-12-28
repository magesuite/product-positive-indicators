<?php

namespace MageSuite\ProductPositiveIndicators\Block\FreeShipping;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_ProductPositiveIndicators::freeshipping/product.phtml';

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface
     */
    protected $freeShippingService;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper,
        \MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface $freeShippingService,
        array $data = []
    ) {
        $this->productHelper = $productHelper;
        $this->freeShippingService = $freeShippingService;
        parent::__construct($context, $data);
    }

    public function isFreeShippingAvailable()
    {
        $product = $this->productHelper->getProduct();

        if (!$product) {
            return false;
        }

        return $this->freeShippingService->isFreeShipped($product);
    }

    public function showTextNoteOnProductsDetailpage()
    {
        return $this->freeShippingService->showTextNoteOnProductsDetailpage();
    }

    public function showBadgeOnProductsDetailpage()
    {
        return $this->freeShippingService->showBadgeOnProductsDetailpage();
    }
}
