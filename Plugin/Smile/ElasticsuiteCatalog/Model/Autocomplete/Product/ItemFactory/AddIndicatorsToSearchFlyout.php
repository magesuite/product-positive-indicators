<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Plugin\Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory;

class AddIndicatorsToSearchFlyout
{
    protected \MageSuite\ProductPositiveIndicators\Service\FreeShipping $freeShipping;

    protected \MageSuite\ProductPositiveIndicators\Helper\Configuration\FreeShipping $freeShippingConfiguration;

    public function __construct(
        \MageSuite\ProductPositiveIndicators\Service\FreeShipping $freeShipping,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\FreeShipping $freeShippingConfiguration
    ) {
        $this->freeShipping = $freeShipping;
        $this->freeShippingConfiguration = $freeShippingConfiguration;
    }

    public function afterCreate(\Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject, $result, array $data)
    {
        $product = $data['product'];

        $result->unsetData('popular_icon');

        if ($product->getPopularIcon()) {
            $result->setData('popular_icon', __('Popular'));
        }

        $result->unsetData('free_shipping');

        if ($this->freeShippingConfiguration->showInSearchAutosuggest() && $this->freeShipping->isFreeShipped($product)) {
            $result->setData('free_shipping', __('Free Shipping'));
        }

        return $result;
    }
}
