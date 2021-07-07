<?php
declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Plugin;

class AddIndicatorsToSearchFlyout
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\FreeShipping
     */
    protected $freeShipping;

    public function __construct(\MageSuite\ProductPositiveIndicators\Service\FreeShipping $freeShipping)
    {
        $this->freeShipping = $freeShipping;
    }

    public function afterCreate(\Smile\ElasticsuiteCatalog\Model\Autocomplete\Product\ItemFactory $subject, $result, array $data)
    {
        $product = $data['product'];

        if ($product->getPopularIcon()) {
            $result->setData('popular_icon', __('Popular'));
        } else {
            $result->unsetData('popular_icon');
        }

        if ($this->freeShipping->showInSearchAutosuggest() && $this->freeShipping->isFreeShipped($product)) {
            $result->setData('free_shipping', __('Free Shipping'));
        } else {
            $result->unsetData('free_shipping');
        }

        return $result;
    }
}
