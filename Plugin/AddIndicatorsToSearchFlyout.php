<?php
declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Plugin;

class AddIndicatorsToSearchFlyout
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\FreeShipping
     */
    protected $freeShipping;

    public function __construct(
        \MageSuite\ProductPositiveIndicators\Service\FreeShipping $freeShipping
    ){
        $this->freeShipping = $freeShipping;
    }

    public function beforeCreate(
        \Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject,
        array $data
    ): array {
        $product = $data['product'];

        if ($product->getPopularIcon()) {
            $data['popular_icon'] = __('Popular');
        }

        if ($this->freeShipping->showInSearchAutosuggest() && $this->freeShipping->isFreeShipped($product)) {
            $data['free_shipping'] = __('Free Shipping');
        }

        return [$data];
    }
}
