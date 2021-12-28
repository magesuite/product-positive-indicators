<?php

namespace MageSuite\ProductPositiveIndicators\Plugin;

/**
 * This plugin calculate top_attribute_min_value only if top_attribute_sign is %
 * and frontend input type is `select` because the option values are known
 * in case `text` types there is cronjob that calculate top_attribute_min_value
 */
class CalculateTopAttribute
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Parser\TopAttributeInterface
     */
    private $parser;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    private $swatchHelper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    public function __construct(\MageSuite\ProductPositiveIndicators\Parser\TopAttributeInterface $parser, \Magento\Swatches\Helper\Data $swatchHelper, \Magento\Framework\Serialize\Serializer\Json $serializer)
    {
        $this->parser = $parser;
        $this->swatchHelper = $swatchHelper;
        $this->serializer = $serializer;
    }

    public function beforeSave(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        // serialize top attribute values
        $topAttributeValues = $attribute->getTopAttributeValue();
        $attribute->setTopAttributeValue($this->serializer->serialize($topAttributeValues));

        // skip if sign is not % or attribute type is not select
        if ($attribute->getTopAttributeSign() != '%' || !in_array($attribute->getFrontendInput(), ["select", "multiselect"])) {
            $attribute->setTopAttributeMinValue("");
            return [];
        }

        // get attribute options with special check for swatch
        $options = $attribute->getData('option');
        if ($this->swatchHelper->isVisualSwatch($attribute)) {
            $options = $attribute->getData('optionvisual');
        } elseif ($this->swatchHelper->isTextSwatch($attribute)) {
            $options = $attribute->getData('optiontext');
        }
        $attributeOptions = $this->getAttributeOptionValues($options);

        // calculate top attribute min value for an each store
        $topAttributeMinValues = $this->parser->calculateTopAttributeMinValue($topAttributeValues, $attributeOptions);
        $attribute->setTopAttributeMinValue($this->serializer->serialize($topAttributeMinValues));

        return [];
    }

    public function getAttributeOptionValues($options)
    {
        $optionValues = [];
        if (empty($options) || empty($options["value"])) {
            return $optionValues;
        }

        foreach ($options["value"] as $opt) {
            foreach ($opt as $storeId => $optionValue) {
                $optionValues[$storeId][] = $optionValue;
            }
        }

        return $optionValues;
    }
}
