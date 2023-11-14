<?php

namespace MageSuite\ProductPositiveIndicators\Test\Unit\Classes;

class TopAttributeParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Parser\TopAttribute
     */
    private $topAttributeParser;

    protected function setUp()
    {
        $this->topAttributeParser = new \MageSuite\ProductPositiveIndicators\Parser\TopAttribute();
    }

    /**
     * @param $isTrue
     * @param $productAttributeValue
     * @param $sign
     * @param $value
     * @param $calculatedValue
     * @param $isMultiselect
     * @dataProvider parseProvider
     */
    public function testParse($isTrue, $productAttributeValue, $sign, $value, $calculatedValue, $isMultiselect)
    {
        $this->assertEquals($isTrue, $this->topAttributeParser->parse($productAttributeValue, $sign, $value, $calculatedValue, $isMultiselect));
    }

    public function parseProvider()
    {
        return [
            'true less than' => [true, "60h", "<", "60,5h", "", false],
            'false less than' => [false, "60h", "<", "60 h", "", false],
            'true less than or equal' => [true, "60h", "<=", "60 h", "", false],
            'true greater than' => [true, "60s", ">", "30s", "", false],
            'false greater than' => [false, "60s", ">", "60 s", "", false],
            'true greater than or equal' => [true, "60h", ">=", "60 h", "", false],
            'true equal' => [true, "70.0000", "=", "70", "", false],
            'true equal spaces' => [true, "70.0000", "=", " 70 ", "", false],
            'false equal' => [false, "FullHD", "=", "Something else", "", false],
            'true in top best x%' => [true, "60h", "%", "", "59.33", false],
            'false in top best x%' => [false, "60h", "%", "", "60.33", false],
            'true in array' => [true, "60h", "[]", "40h,60h,70h,80h", "", false],
            'false in array' => [false, "60h", "[]", "40h,50h,70h,80h", "", false],
            'true multiselect' => [true, "10, 20", ">", "15", "", true],
            'false multiselect' => [false, "10, 20", "<", "5", "", true]
        ];
    }

    /**
     * @param float $result
     * @param string $value
     * @dataProvider parseToFloatProvider
     */
    public function testParseToFloat($result, $value)
    {
        $this->assertEquals($result, $this->topAttributeParser->parseToFloat($value));
    }

    public function parseToFloatProvider()
    {
        return [
            'float' => [1.20, 1.20],
            'float as string' => [1.20, "1.20"],
            'float with comma' => [1.20, "1,20"],
            'float with sign and comma' => [-1.20, "-1,20"],
            'string' => [0, "xyz"],
            'string with float' => [60.2, "60,2h"],
        ];
    }

    /**
     * @param float $result
     * @param array $percents
     * @param array $values
     * @dataProvider calculateTopAttributeMinValueProvider
     */
    public function testCalculateTopAttributeMinValue($result, array $percents, array $values)
    {
        $this->assertEquals($result, $this->topAttributeParser->calculateTopAttributeMinValue($percents, $values));
    }

    public function calculateTopAttributeMinValueProvider()
    {
        return [
            'one store' => [
                ["1" => 55], // result - minimum value for store wanted
                ["1" => 10], // percents
                ["0" => [10, 20, 50], "1" => ["", "", 60]] // values
            ],
            'two stores' => [
                ["1" => 46, "2" => 550], // result
                ["1" => 10, "2" => 50], // percents
                ["0" => ["", 20, 50], "1" => [10, "", ""], "2" => [100, 500, 1000]] // values
            ],
        ];
    }
}
