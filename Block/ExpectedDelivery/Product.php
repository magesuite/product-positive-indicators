<?php

namespace MageSuite\ProductPositiveIndicators\Block\ExpectedDelivery;

class Product extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_CONFIGURATION_KEY = 'expected_delivery';
    const CACHE_KEY = 'indicator_expected_delivery_%s_%s_%s';

    protected $_template = 'expecteddelivery/product.phtml';

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery
     */
    protected $expectedDeliveryDataProvider;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration $configuration,
        \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery $expectedDeliveryDataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->configuration = $configuration;
        $this->expectedDeliveryDataProvider = $expectedDeliveryDataProvider;
    }

    public function getDeliveryData()
    {
        $config = $this->configuration->getConfig(self::XML_PATH_CONFIGURATION_KEY);

        if(!$config['active'] or !$config['delivery_today_time']){
            return false;
        }

        $product = $this->getProduct();

        if(!$product){
            return false;
        }

        $cacheKey = $this->prepareCacheKey($product->getId());

        $deliveryData = unserialize($this->cache->load($cacheKey));

        if(!$deliveryData){
            $deliveryData = $this->expectedDeliveryDataProvider->prepareDeliveryData($config, $product);
            $this->cache->save(serialize($deliveryData), $cacheKey);
        }

        return $deliveryData;
    }

    protected function getProduct()
    {
        $product = $this->registry->registry('product');

        if(!$product){
            return false;
        }

        return $product;
    }

    protected function prepareCacheKey(int $productId)
    {
        return sprintf(
            self::CACHE_KEY,
            $productId,
            $this->storeManager->getStore()->getId(),
            date('d')
        );
    }

}
