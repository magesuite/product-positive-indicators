<?php

namespace MageSuite\ProductPositiveIndicators\Block\FreeShipping;

class Product extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_ProductPositiveIndicators::freeshipping/product.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface
     */
    protected $freeShippingService;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface $freeShippingService,
        array $data = []
    )
    {
        $this->registry = $registry;
        $this->freeShippingService = $freeShippingService;
        parent::__construct($context, $data);
    }

    public function isFreeShippingAvailable()
    {
        $product = $this->getProduct();

        if(!$product){
            return false;
        }

        return $this->freeShippingService->isFreeShipped($product);
    }

    public function showTextNoteOnProductsDetailpage(){
        return $this->freeShippingService->showTextNoteOnProductsDetailpage();
    }

    public function showBadgeOnProductsDetailpage(){
        return $this->freeShippingService->showBadgeOnProductsDetailpage();
    }

    protected function getProduct()
    {
        $product = $this->registry->registry('product');

        if(!$product){
            return false;
        }

        return $product;
    }

}