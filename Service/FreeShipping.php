<?php

namespace MageSuite\ProductPositiveIndicators\Service;


class FreeShipping implements FreeShippingInterface
{

    /**
     * @var \Magento\Shipping\Model\Config
     */
    private $shippingConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $session,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->session = $session;
        $this->shippingConfig = $shippingConfig;
        $this->productRepository = $productRepository;
    }

    public function showInProductTiles(){
        return $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/show_in_product_tiles',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function showTextNoteOnProductsDetailpage(){
        return $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/show_text_note_on_products_detailpage',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function showBadgeOnProductsDetailpage(){
        return $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/show_badge_on_products_detailpage',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function showInSearchAutosuggest(){
        return $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/show_in_search_autosuggest',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isFreeShipped($product)
    {
        if($this->getFreeShippingValue() === false) {
            return false;
        }

        if(!$product){
            return false;
        }

        $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();

        if(!$finalPrice){
            return false;
        }

        return $finalPrice >= $this->getFreeShippingValue();
    }

    public function getShippingMethodsWithFreeShipping()
    {
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