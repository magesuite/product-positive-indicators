<?php

namespace MageSuite\ProductPositiveIndicators\Block\ExpectedDelivery;

class Product extends \Magento\Framework\View\Element\Template
{

    const CACHE_KEY = 'indicator_expected_delivery_%s_%s_%s';

    protected $_template = 'expecteddelivery/product.phtml';

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery
     */
    protected $configuration;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery
     */
    protected $expectedDeliveryDataProvider;

    protected $deliveryData = null;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery $configuration,
        \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery $expectedDeliveryDataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->productHelper = $productHelper;
        $this->storeManager = $storeManager;
        $this->configuration = $configuration;
        $this->expectedDeliveryDataProvider = $expectedDeliveryDataProvider;
    }

    public function isEnabled()
    {
        $deliveryData = $this->getDeliveryData();

        return empty($deliveryData) ? false : true;
    }

    public function getMaxTimeToday()
    {
        return $this->getDeliveryDataByKey('max_today_time');
    }

    public function getShipDayTime()
    {
        return $this->getDeliveryDataByKey('ship_day_time');
    }

    public function getShipDayName()
    {
        return $this->getDeliveryDataByKey('ship_day_name');
    }

    public function getNextShipDayTime()
    {
        return $this->getDeliveryDataByKey('next_ship_day_time');
    }

    public function getNextShipDayName()
    {
        return $this->getDeliveryDataByKey('next_ship_day_name');
    }

    public function getUtcOffset()
    {
        return $this->getDeliveryDataByKey('utc_offset');
    }

    protected function getDeliveryDataByKey($key)
    {
        $deliveryData = $this->getDeliveryData();

        if (empty($deliveryData)) {
            return null;
        }

        return $deliveryData->getData($key);
    }

    protected function getDeliveryData()
    {
        if (!$this->configuration->isEnabled() || !$this->configuration->getDeliveryTodayTime()) {
            return false;
        }

        if ($this->deliveryData === null) {
            $product = $this->productHelper->getProduct();

            if (!$product) {
                return $this->deliveryData;
            }

            $cacheKey = $this->getCacheKeyForProductId($product->getId());
            $deliveryData = $this->cache->load($cacheKey);

            if ($deliveryData) {
                $deliveryData = new \Magento\Framework\DataObject(
                    $this->serializer->unserialize($deliveryData)
                );
            } else {
                $deliveryData = $this->expectedDeliveryDataProvider->getDeliveryData($product);

                if ($deliveryData != null) {
                    $this->cache->save(
                        $this->serializer->serialize($deliveryData->toArray()),
                        $cacheKey,
                        [\Magento\Framework\App\Config::CACHE_TAG]
                    );
                }
            }

            $this->deliveryData = $deliveryData;
        }

        return $this->deliveryData;
    }

    protected function getCacheKeyForProductId(int $productId)
    {
        return sprintf(
            self::CACHE_KEY,
            $productId,
            $this->storeManager->getStore()->getId(),
            date('d')
        );
    }
}
