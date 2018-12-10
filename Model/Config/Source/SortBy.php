<?php

namespace MageSuite\ProductPositiveIndicators\Model\Config\Source;

class SortBy implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    protected $options = null;

    public function __construct(\Magento\Catalog\Model\Config $catalogConfig)
    {
        $this->catalogConfig = $catalogConfig;
    }

    public function toOptionArray()
    {
        if ($this->options === null) {

            $this->options = [
                ['label' => __('Position'), 'value' => 'position']
            ];

            foreach ($this->catalogConfig->getAttributesUsedForSortBy() as $attribute) {
                $this->options[] = [
                    'label' => __($attribute['frontend_label']),
                    'value' => $attribute['attribute_code'],
                ];
            }
        }

        return $this->options;
    }
}
