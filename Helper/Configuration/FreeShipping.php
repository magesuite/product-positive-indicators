<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Helper\Configuration;

class FreeShipping
{
    public const XML_PATH_FREE_SHIPPING_SHOW_IN_PRODUCT_TILES = 'positive_indicators/free_shipping/show_in_product_tiles';
    public const XML_PATH_FREE_SHIPPING_SHOW_TEXT_NOTE_ON_PRODUCTS_DETAILPAGE = 'positive_indicators/free_shipping/show_text_note_on_products_detailpage';
    public const XML_PATH_FREE_SHIPPING_SHOW_BADGE_ON_PRODUCTS_DETAILPAGE = 'positive_indicators/free_shipping/show_badge_on_products_detailpage';
    public const XML_PATH_FREE_SHIPPING_SHOW_IN_SEARCH_AUTOSUGGEST = 'positive_indicators/free_shipping/show_in_search_autosuggest';

    public function __construct(
        protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {}

    public function showInProductTiles(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_FREE_SHIPPING_SHOW_IN_PRODUCT_TILES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function showTextNoteOnProductsDetailpage(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_FREE_SHIPPING_SHOW_TEXT_NOTE_ON_PRODUCTS_DETAILPAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function showBadgeOnProductsDetailpage(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_FREE_SHIPPING_SHOW_BADGE_ON_PRODUCTS_DETAILPAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function showInSearchAutosuggest(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_FREE_SHIPPING_SHOW_IN_SEARCH_AUTOSUGGEST, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function isCarrierFreeShippingEnabled(string $carrierCode): bool
    {
        $field = $carrierCode === 'freeshipping' ? 'active' : 'free_shipping_enable';

        return (bool)$this->scopeConfig->getValue(
            'carriers/' . $carrierCode . '/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCarrierFreeShippingSubtotal(string $carrierCode): mixed
    {
        return $this->scopeConfig->getValue(
            'carriers/' . $carrierCode . '/free_shipping_subtotal',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCarrierTitle(string $carrierCode): mixed
    {
        return $this->scopeConfig->getValue(
            'carriers/' . $carrierCode . '/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getDefaultShippingMethod(): string|false
    {
        $defaultShippingMethod = $this->scopeConfig->getValue(
            'positive_indicators/free_shipping/free_shipping_method',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $defaultShippingMethod ?: false;
    }

    public function isCarrierRestrictedToSpecificCountries(string $carrierCode): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'carriers/' . $carrierCode . '/sallowspecific',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getDefaultCountry(): mixed
    {
        return $this->scopeConfig->getValue('general/country/default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getCarrierSpecificCountries(string $carrierCode): mixed
    {
        return $this->scopeConfig->getValue(
            'carriers/' . $carrierCode . '/specificcountry',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
