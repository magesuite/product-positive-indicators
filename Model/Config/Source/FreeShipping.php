<?php
namespace MageSuite\ProductPositiveIndicators\Model\Config\Source;

class FreeShipping implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface
     */
    protected $freeShippingService;

    public function __construct(\MageSuite\ProductPositiveIndicators\Service\FreeShippingInterface $freeShippingService)
    {
        $this->freeShippingService = $freeShippingService;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $array = $this->toArray();

        foreach ($array as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }

        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [0 => __('None')];
        foreach ($this->freeShippingService->getShippingMethodsWithFreeShipping() as $code => $model) {
            $array[$code] = $model['title'];
        }
        return $array;
    }
}
