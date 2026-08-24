<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Service;

class FreeShipping implements FreeShippingInterface
{
    protected array $freeShippedProducts = [];
    protected ?array $shippingMethods = null;

    public function __construct(
        protected \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        protected \Magento\Checkout\Model\Session $session,
        protected \MageSuite\ProductPositiveIndicators\Helper\Configuration\FreeShipping $configuration,
        protected \Magento\Shipping\Model\Config $shippingConfig
    ) {}

    public function isFreeShipped(\Magento\Catalog\Api\Data\ProductInterface $product): bool
    {
        $productId = (int)$product->getId();

        if (!array_key_exists($productId, $this->freeShippedProducts)) {
            $this->freeShippedProducts[$productId] = $this->calculateIsFreeShipped($product);
        }

        return $this->freeShippedProducts[$productId];
    }

    public function calculateIsFreeShipped(\Magento\Catalog\Api\Data\ProductInterface $product): bool
    {
        $freeShippingValue = $this->getFreeShippingValue();

        if ($freeShippingValue === false) {
            return false;
        }

        $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();

        if (!$finalPrice) {
            return false;
        }

        return (float)$finalPrice >= (float)$freeShippingValue;
    }

    public function getShippingMethodsWithFreeShipping(): array
    {
        if ($this->shippingMethods !== null) {
            return $this->shippingMethods;
        }

        $activeCarriers = $this->shippingConfig->getActiveCarriers();
        $this->shippingMethods = [];

        foreach (array_keys($activeCarriers) as $code) {
            if (!$this->configuration->isCarrierFreeShippingEnabled($code)) {
                continue;
            }

            $this->shippingMethods[$code] = [
                'title' => $this->configuration->getCarrierTitle($code),
                'value' => $this->configuration->getCarrierFreeShippingSubtotal($code)
            ];
        }

        return $this->shippingMethods;
    }

    protected function getFreeShippingValue(): bool|string
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

    private function getDefaultShippingMethod(): string|false
    {
        $defaultShippingMethod = $this->configuration->getDefaultShippingMethod();

        if (!$defaultShippingMethod) {
            return false;
        }

        $isAllAllowedCountries = !$this->configuration->isCarrierRestrictedToSpecificCountries($defaultShippingMethod);

        if ($isAllAllowedCountries) {
            return $defaultShippingMethod;
        }

        $defaultCountry = $this->configuration->getDefaultCountry();
        $shipToSpecifCountry = $this->configuration->getCarrierSpecificCountries($defaultShippingMethod);

        if (!$defaultCountry || !$shipToSpecifCountry) {
            return false;
        }

        if (strpos($shipToSpecifCountry, $defaultCountry) !== false) {
            return $defaultShippingMethod;
        }

        return false;
    }

    protected function getSelectedShippingMethod(): string|false
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
