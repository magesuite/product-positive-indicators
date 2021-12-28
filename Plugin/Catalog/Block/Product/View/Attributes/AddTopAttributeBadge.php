<?php

namespace MageSuite\ProductPositiveIndicators\Plugin\Catalog\Block\Product\View\Attributes;

class AddTopAttributeBadge
{
    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\TopAttribute
     */
    protected $topAttributeHelper;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Helper\Configuration\TopAttributes
     */
    protected $topAttributesConfiguration;

    public function __construct(
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \MageSuite\ProductPositiveIndicators\Helper\TopAttribute $topAttributeHelper,
        \MageSuite\ProductPositiveIndicators\Helper\Configuration\TopAttributes $topAttributesConfiguration
    ) {
        $this->blockFactory = $blockFactory;
        $this->topAttributeHelper = $topAttributeHelper;
        $this->topAttributesConfiguration = $topAttributesConfiguration;
    }

    public function afterGetAdditionalData($subject, $result)
    {
        if (!$this->topAttributesConfiguration->isEnabled()) {
            return $result;
        }

        $topAttributesBlockHtml = $this->blockFactory->createBlock(\MageSuite\ProductPositiveIndicators\Block\TopAttributes\Badge::class)->toHtml();

        foreach ($result as $attributeCode => $data) {

            if (!$this->topAttributeHelper->checkTopAttribute($attributeCode, $data['value'])) {
                continue;
            }

            $result[$attributeCode]['value'] = $this->addTopAttributeBadge($result[$attributeCode]['value'], $topAttributesBlockHtml);
        }

        return $result;
    }

    protected function addTopAttributeBadge($value, $badgeHtml)
    {
        return $value . $badgeHtml;
    }
}
