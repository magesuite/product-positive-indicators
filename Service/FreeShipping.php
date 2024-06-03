<?php

namespace MageSuite\ProductPositiveIndicators\Service;

class FreeShipping implements FreeShippingInterface
{
    protected const CACHE_KEY = 'free_shipping_methods';
    protected ?array $freeShippingValue = null;

    protected \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected \Magento\Checkout\Model\Session $session;
    protected \Magento\Framework\App\CacheInterface $cacheManager;
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;
    protected \Magento\Framework\Serialize\SerializerInterface $serializer;
    protected \Magento\Shipping\Model\Config $shippingConfig;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Checkout\Model\Session $session,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Shipping\Model\Config $shippingConfig
    ) {
        $this->cacheManager = $cacheManager;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->session = $session;
        $this->shippingConfig = $shippingConfig;
    }

    public function showInProductTiles()
    {
        return $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/show_in_product_tiles',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function showTextNoteOnProductsDetailpage()
    {
        return $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/show_text_note_on_products_detailpage',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function showBadgeOnProductsDetailpage()
    {
        return $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/show_badge_on_products_detailpage',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function showInSearchAutosuggest()
    {
        return $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/show_in_search_autosuggest',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isFreeShipped($product)
    {
        if (isset($this->freeShippingValue[$product->getId()]) && $this->freeShippingValue[$product->getId()] !== null) {
            return $this->freeShippingValue[$product->getId()];
        }

        $this->freeShippingValue[$product->getId()] = $this->getFreeShippingValue();
        if ($this->freeShippingValue[$product->getId()] === false) {
            return false;
        }

        if (!$product) {
            return false;
        }

        $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();

        if (!$finalPrice) {
            return false;
        }

        return $finalPrice >= $this->getFreeShippingValue();
    }

    public function removeShippingMethodsWithFreeShippingFromCache()
    {
        $this->cacheManager->remove(self::CACHE_KEY);
    }

    public function getShippingMethodsWithFreeShipping(bool $force = false)
    {
        $cachedMethods = $this->cacheManager->load(self::CACHE_KEY);

        if (!$force && $cachedMethods) {
            return $this->serializer->unserialize($cachedMethods);
        }

        $activeCarriers = $this->shippingConfig->getActiveCarriers();
        $methods = [];

        foreach ($activeCarriers as $code => $model) {
            $activeField = $code == 'freeshipping' ? 'active' : 'free_shipping_enable';
            $isFreeShippingEnabled = $this->scopeConfig->getValue(
                'carriers/' . $code . '/' . $activeField,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if (!$isFreeShippingEnabled) {
                continue;
            }

            $freeShippingSubtotal = $this->scopeConfig->getValue(
                'carriers/' . $code . '/free_shipping_subtotal',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $freeShippingTitle = $this->scopeConfig->getValue(
                'carriers/' . $code . '/title',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $methods[$code] = [
                'title' => $freeShippingTitle,
                'value' => $freeShippingSubtotal
            ];
        }

        $serializedMethods = $this->serializer->serialize($methods);
        $this->cacheManager->save($serializedMethods, self::CACHE_KEY);

        return $methods;
    }

    private function getFreeShippingValue()
    {
        $activeMethods = $this->getShippingMethodsWithFreeShipping();
        $selectedShippingMethod = $this->getSelectedShippingMethod();

        if (!$selectedShippingMethod) {
            return false;
        }

        if (!isset($activeMethods[$selectedShippingMethod])) {
            return false;
        }

        if (!isset($activeMethods[$selectedShippingMethod]['value'])) {
            return false;
        }

        return $activeMethods[$selectedShippingMethod]['value'];
    }

    private function getDefaultShippingMethod()
    {
        $defaultShippingMethod = $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/free_shipping_method',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$defaultShippingMethod) {
            return false;
        }

        $isAllAllowedCountries = !$this->scopeConfig->getValue(
            'carriers/' . $defaultShippingMethod . '/sallowspecific',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($isAllAllowedCountries) {
            return $defaultShippingMethod;
        }

        $defaultCountry = $this->scopeConfig->getValue(
            'general/country/default',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $shipToSpecifCountry = $this->scopeConfig->getValue(
            'carriers/' . $defaultShippingMethod . '/specificcountry',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (strpos($shipToSpecifCountry, $defaultCountry) !== false) {
            return $defaultShippingMethod;
        }

        return false;
    }

    private function getSelectedShippingMethod()
    {
        if (!$this->session->hasQuote()) {
            return $this->getDefaultShippingMethod();
        }

        $quote = $this->session->getQuote();

        if (!$quote) {
            return $this->getDefaultShippingMethod();
        }

        $address = $quote->getShippingAddress();

        if (!$address) {
            return $this->getDefaultShippingMethod();
        }

        $shippingMethod = $address->getShippingMethod();

        if (!$shippingMethod) {
            return $this->getDefaultShippingMethod();
        }

        list($carrierCode, $method) = explode('_', $shippingMethod, 2);

        return $carrierCode;
    }
}
