<?php

namespace MageSuite\ProductPositiveIndicators\Parser;

class TopAttribute implements TopAttributeInterface
{
    /**
     * Parser checks if top indicator should be shown or not
     * @param string $productAttributeValue
     * @param string $sign
     * @param string $value
     * @param string $minValue
     * @param bool $isMultiselect
     * @return bool
     */
    public function parse($productAttributeValue, $sign, $value, $minValue, $isMultiselect = false)
    {
        if($isMultiselect) {
            $productAttributeValues = explode(', ', $productAttributeValue);

            foreach ($productAttributeValues as $pav) {
                if ($this->parse($pav, $sign, $value, $minValue)) {
                    return true;
                }
            }

            return false;
        }

        $productAttributeValueFloat = $this->parseToFloat($productAttributeValue);
        $valueFloat = $this->parseToFloat($value);

        switch ($sign) {
            case '%' :
                return !empty($minValue) ?
                    $productAttributeValueFloat >= $minValue : false;
            case '[]' :
                return in_array(trim($productAttributeValue), array_map('trim', explode(',', $value)));
            case '=' :
                return trim($productAttributeValue) == trim($value);
            case '<' :
                return $productAttributeValueFloat < $valueFloat;
            case '<=' :
                return $productAttributeValueFloat <= $valueFloat;
            case '>' :
                return $productAttributeValueFloat > $valueFloat;
            case '>=' :
                return $productAttributeValueFloat >= $valueFloat;
        }

        return false;
    }

    /**
     * @param string $value
     * @return float
     */
    public function parseToFloat($value)
    {
        $valueRegEx = 0;
        if (preg_match('/[+-]?([0-9]*[.,])?[0-9]+/', $value, $matches)) {
            $valueRegEx = str_replace(',', '.', $matches[0]);
        }
        return floatval($valueRegEx);
    }

    /**
     * Calculate top attribute min value in case of top x% for an each store view
     *
     * @param array $percents
     * @param array $values
     * @return array
     */
    public function calculateTopAttributeMinValue(array $percents, array $values)
    {
        $minValues = [];
        if(empty($values) || empty($percents)){
            return $minValues;
        }

        $defaultPercent = isset($percents[self::DEFAULT_STORE_INDEX]) ? $percents[self::DEFAULT_STORE_INDEX] : [];
        foreach($percents as $storeId => $percent){
            $percents[$storeId] = $this->parseToFloat($percent == "" ? $defaultPercent : $percent);
        }

        $defaultValues = isset($values[self::DEFAULT_STORE_INDEX]) ? $values[self::DEFAULT_STORE_INDEX] : [];
        // fill values with default variable when value is empty
        foreach($values as $storeId => $storeValues){
            foreach($storeValues as $key => $value) {
                if($value == "" && isset($defaultValues[$key])) {
                    $value = $defaultValues[$key];
                }

                $values[$storeId][$key] = $this->parseToFloat($value);
            }
        }

        foreach ($percents as $storeId => $percent) {
            $min = min($values[$storeId]);
            $max = max($values[$storeId]);
            $minValues[$storeId] = $max - ($max - $min) * $percent / 100;
        }

        return $minValues;
    }
}
