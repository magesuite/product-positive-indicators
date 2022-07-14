<?php

namespace MageSuite\ProductPositiveIndicators\Block\Tile\Fragment\Badge;

class Popular implements \MageSuite\ProductTile\Block\Tile\Fragment\BadgeInterface
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

    public function __construct(\MageSuite\ProductPositiveIndicators\Helper\Product $productHelper)
    {
        $this->productHelper = $productHelper;
    }

    /**
     * @return boolean
     */
    public function isVisible(\MageSuite\ProductTile\Block\Tile $tile)
    {
        return $tile->getProductEntity()->getPopularIcon();
    }

    /**
     * @return string
     */
    public function getValue(\MageSuite\ProductTile\Block\Tile $tile)
    {
        return '';
    }

    /**
     * @return string
     */
    public function getCssModifier(\MageSuite\ProductTile\Block\Tile $tile)
    {
        return '';
    }

    public function getCategoriesIds(\MageSuite\ProductTile\Block\Tile $tile) {
        $categoriesIds = explode(',', $tile->getProductEntity()->getPopularIconCategories());

        return array_map('intval', $categoriesIds);
    }
}
