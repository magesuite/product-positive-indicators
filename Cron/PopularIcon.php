<?php
namespace MageSuite\ProductPositiveIndicators\Cron;

class PopularIcon
{
    const DEFAULT_FRONTEND_STORE_ID = 1;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Model\PopularIconProducts
     */
    protected $popularIconProducts;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductPositiveIndicators\Model\PopularIconProducts $popularIconProducts
    )
    {
        $this->storeManager = $storeManager;
        $this->popularIconProducts = $popularIconProducts;
    }

    public function execute()
    {
        $this->storeManager->setCurrentStore(self::DEFAULT_FRONTEND_STORE_ID);

        $this->popularIconProducts->execute();
    }
}
