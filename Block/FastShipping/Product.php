<?php

namespace MageSuite\ProductPositiveIndicators\Block\FastShipping;

class Product extends \Magento\Framework\View\Element\Template
{
    const CACHE_KEY = 'indicator_fast_shipping_%s_%s';

    protected $_template = 'MageSuite_ProductPositiveIndicators::fastshipping/product.phtml';

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping
     */
    protected $configuration;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping
     */
    protected $fastShippingDataProvider;

    protected $deliveryData = null;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping $configuration,
        \MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping $fastShippingDataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->configuration = $configuration;
        $this->fastShippingDataProvider = $fastShippingDataProvider;
    }

    public function isEnabled()
    {
        return $this->configuration->isEnabled();
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

    public function isNextDayTomorrow()
    {
        return $this->getDeliveryDataByKey('is_next_day_tomorrow');
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
            $cacheKey = $this->getCacheKeyForIndicator();
            $deliveryData = $this->cache->load($cacheKey);

            if ($deliveryData) {
                $deliveryData = new \Magento\Framework\DataObject(
                    $this->serializer->unserialize($deliveryData)
                );
            } else {
                $deliveryData = $this->fastShippingDataProvider->getDeliveryData();
                $this->cache->save(
                    $this->serializer->serialize($deliveryData->toArray()),
                    $cacheKey,
                    [\Magento\Framework\App\Config::CACHE_TAG]
                );
            }

            $this->deliveryData = $deliveryData;
        }

        return $this->deliveryData;
    }

    protected function getCacheKeyForIndicator()
    {
        return sprintf(
            self::CACHE_KEY,
            $this->storeManager->getStore()->getId(),
            date('d')
        );
    }
}
