<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Block\Tile\Fragment\Badge;

class Popular implements \MageSuite\ProductTile\Block\Tile\Fragment\BadgeInterface
{
    protected \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper;

    public function __construct(\MageSuite\ProductPositiveIndicators\Helper\Product $productHelper)
    {
        $this->productHelper = $productHelper;
    }

    public function isVisible(\MageSuite\ProductTile\Block\Tile $tile): bool
    {
        return $tile->getProductEntity()->getPopularIcon();
    }

    public function getValue(\MageSuite\ProductTile\Block\Tile $tile): string
    {
        return '';
    }

    public function getCssModifier(\MageSuite\ProductTile\Block\Tile $tile): string
    {
        return '';
    }

    public function getCategoriesIds(\MageSuite\ProductTile\Block\Tile $tile): array
    {
        $popularIconCategories = $tile->getProductEntity()->getPopularIconCategories();

        if (empty($popularIconCategories)) {
            return [];
        }

        $categoriesIds = explode(',', $popularIconCategories);

        return array_map('intval', $categoriesIds);
    }
}
