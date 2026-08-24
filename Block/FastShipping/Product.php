<?php

declare(strict_types=1);

namespace MageSuite\ProductPositiveIndicators\Block\FastShipping;

class Product extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'MageSuite_ProductPositiveIndicators::fastshipping/product.phtml'; // phpcs:ignore

    protected ?\Magento\Framework\DataObject $deliveryData = null;

    protected bool $deliveryDataCalculated = false;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        protected \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper,
        protected \Magento\Store\Model\StoreManagerInterface $storeManager,
        protected \MageSuite\ProductPositiveIndicators\Helper\Configuration\FastShipping $configuration,
        protected \MageSuite\ProductPositiveIndicators\Service\DataProvider\FastShipping $fastShippingDataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isEnabled(): bool
    {
        return (bool)$this->configuration->isEnabled();
    }

    public function canDisplayFastShippingText(): bool
    {
        $product = $this->productHelper->getProduct();

        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return true;
        }

        return $product->isSaleable();
    }

    public function getMaxTimeToday(): mixed
    {
        return $this->getDeliveryDataByKey('max_today_time');
    }

    public function getShipDayTime(): mixed
    {
        return $this->getDeliveryDataByKey('ship_day_time');
    }

    public function getShipDayName(): mixed
    {
        return $this->getDeliveryDataByKey('ship_day_name');
    }

    public function isNextDayTomorrow(): mixed
    {
        return $this->getDeliveryDataByKey('is_next_day_tomorrow');
    }

    public function getNextShipDayTime(): mixed
    {
        return $this->getDeliveryDataByKey('next_ship_day_time');
    }

    public function getNextShipDayName(): mixed
    {
        return $this->getDeliveryDataByKey('next_ship_day_name');
    }

    public function getUtcOffset(): mixed
    {
        return $this->getDeliveryDataByKey('utc_offset');
    }

    protected function getDeliveryDataByKey(string $key): mixed
    {
        $deliveryData = $this->getDeliveryData();

        if (empty($deliveryData)) {
            return null;
        }

        return $deliveryData->getData($key);
    }

    protected function getDeliveryData(): \Magento\Framework\DataObject|bool|null
    {
        if (!$this->configuration->isEnabled() || !$this->configuration->getDeliveryTodayTime()) {
            return false;
        }

        if (!$this->deliveryDataCalculated) {
            $this->deliveryData = $this->fastShippingDataProvider->getDeliveryData();
            $this->deliveryDataCalculated = true;
        }

        return $this->deliveryData;
    }
}
