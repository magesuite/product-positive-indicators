<?php
namespace MageSuite\ProductPositiveIndicators\Cron;

class RecentlyBought
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Model\RecentlyBoughtProducts
     */
    protected $recentlyBoughtProducts;

    public function __construct(
        \MageSuite\ProductPositiveIndicators\Model\RecentlyBoughtProducts $recentlyBoughtProducts
    )
    {
        $this->recentlyBoughtProducts = $recentlyBoughtProducts;
    }

    public function execute()
    {
        $this->recentlyBoughtProducts->execute();
    }
}
