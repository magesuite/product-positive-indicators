<?php

namespace MageSuite\ProductPositiveIndicators\Parser;

interface TopAttributeInterface
{
    const DEFAULT_STORE_INDEX = 0;

    /**
     * Parser checks if top indicator should be shown or not
     * @param string $productAttributeValue
     * @param string $sign
     * @param string $value
     * @param string $minValue
     * @param bool $isMultiselect
     * @return bool
     */
    public function parse($productAttributeValue, $sign, $value, $minValue, $isMultiselect);

    /**
     * @param $value
     * @return float
     */
    public function parseToFloat($value);

    /**
     * Calculate top attribute min value in case of top x% for an each store view
     * @param array $percents
     * @param array $values
     * @return float|int|mixed
     */
    public function calculateTopAttributeMinValue(array $percents, array $values);
}