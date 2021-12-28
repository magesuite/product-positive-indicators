<?php

namespace MageSuite\ProductPositiveIndicators\Helper;

class TopAttribute extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_STORE_INDEX = 0;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Parser\TopAttributeInterface
     */
    protected $parser;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \MageSuite\ProductPositiveIndicators\Parser\TopAttributeInterface $parser
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \MageSuite\ProductPositiveIndicators\Parser\TopAttributeInterface $parser,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->eavConfig = $eavConfig;
        $this->parser = $parser;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * @param string $attributeName
     * @param string $productAttributeValue
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkTopAttribute($attributeName, $productAttributeValue)
    {
        /* @var $attribute \Magento\Catalog\Model\Category\Attribute */
        $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeName);

        if (!$attribute instanceof \Magento\Catalog\Api\Data\ProductAttributeInterface) {
            return false;
        }

        $enabled = $attribute->getData('top_attribute_enabled');
        if (!$enabled) {
            return false;
        }

        $storeId = $this->storeManager->getStore()->getId();
        $sign = $attribute->getData('top_attribute_sign');

        try {
            $values = $this->serializer->unserialize($attribute->getData('top_attribute_value'));
        } catch (\Exception $e) {
            $values = [];
        }
        $storeValue = $this->getStoreValue($values, $storeId);

        try {
            $minValues = $this->serializer->unserialize($attribute->getData('top_attribute_min_value'));
        } catch (\Exception $e) {
            $minValues = [];
        }

        $storeMinValues = $this->getStoreValue($minValues, $storeId);
        $isMultiselect = $attribute->getFrontendInput() === 'multiselect';

        return $this->parser->parse($productAttributeValue, $sign, $storeValue, $storeMinValues, $isMultiselect);
    }

    /**
     * Get value by store id - if value not exist get first as default
     * @param string|array $values
     * @param int $storeId
     * @return mixed
     */
    public function getStoreValue($values, $storeId)
    {
        if (!is_array($values)) {
            return '';
        }

        if (!isset($values[$storeId]) && !isset($values[self::DEFAULT_STORE_INDEX])) {
            return '';
        }

        return isset($values[$storeId]) && $values[$storeId] !== '' ? $values[$storeId] : $values[self::DEFAULT_STORE_INDEX];
    }
}
