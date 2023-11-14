<?php

namespace MageSuite\ProductPositiveIndicators\Plugin;

class AddIndicatorsToSearchFlyout
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    private $productRepository;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\FreeShipping
     */
    private $freeShipping;

    /**
     * AddPopularBadgeToSearchFlyout constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \MageSuite\ProductPositiveIndicators\Service\FreeShipping $freeShipping
    ){
        $this->productRepository = $productRepository;
        $this->freeShipping = $freeShipping;
    }

    public function afterCreate(\Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject, $result, array $data)
    {
        $product = $this->productRepository->get($data['product']->getSku());

        if ($product->getPopularIcon()) {
            $result->setData('popular_icon', __('Popular'));
        }

        if($this->freeShipping->showInSearchAutosuggest() and $this->freeShipping->isFreeShipped($product)){
            $result->setData('free_shipping', __('Free Shipping'));
        }

        return $result;
    }
}
