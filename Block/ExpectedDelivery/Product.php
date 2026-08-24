<?php

namespace MageSuite\ProductPositiveIndicators\Block\ExpectedDelivery;

class Product extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'expecteddelivery/product.phtml';

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Product
     */
    protected $productHelper;

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
        \MageSuite\ProductPositiveIndicators\Helper\Product $productHelper,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\ExpectedDelivery $configuration,
        \MageSuite\ProductPositiveIndicators\Service\DataProvider\ExpectedDelivery $expectedDeliveryDataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productHelper = $productHelper;
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

            $this->deliveryData = $this->expectedDeliveryDataProvider->getDeliveryData($product);
        }

        return $this->deliveryData;
    }
}
