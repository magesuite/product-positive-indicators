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

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
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

    public function getDeliveryData()
    {
        $config = $this->configuration->getConfig(self::XML_PATH_CONFIGURATION_KEY);

        if(!$config->getActive() or !$config->getDeliveryTodayTime()){
            return false;
        }

        $product = $this->productHelper->getProduct();

        if(!$product){
            return false;
        }

        $cacheKey = $this->getCacheKeyForProductId($product->getId());

        $deliveryData = unserialize($this->cache->load($cacheKey));

        if(!$deliveryData){
            $deliveryData = $this->expectedDeliveryDataProvider->getDeliveryData($config, $product);
            $this->cache->save(serialize($deliveryData), $cacheKey);
        }

        return $deliveryData;
    }

    public function getCacheKeyForProductId(int $productId)
    {
        return sprintf(
            self::CACHE_KEY,
            $productId,
            $this->storeManager->getStore()->getId(),
            date('d')
        );
    }

}
