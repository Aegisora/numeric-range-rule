<?php

namespace Aegisora\Rules\Tests\Unit;

use Aegisora\RuleContract\Exceptions\InvalidRuleContextException;
use Aegisora\RuleContract\Models\Context;
use Aegisora\RuleContract\Models\Result;
use Aegisora\RuleContract\RuleInterface;
use Aegisora\Rules\NumericRangeRule;
use PHPUnit\Framework\TestCase;
use stdClass;

class NumericRangeRuleTest extends TestCase
{
    /**
     * @dataProvider getFactoryProvidedData
     */
    public function testFactoryCreatesRule(NumericRangeRule $rule): void
    {
        self::assertInstanceOf(RuleInterface::class, $rule);
    }

    public static function getFactoryProvidedData(): array
    {
        return [
            'greater than' => [
                'rule' => NumericRangeRule::createGreaterThan(3),
            ],
            'greater than or equal to' => [
                'rule' => NumericRangeRule::createGreaterThanOrEqualTo(3),
            ],
            'less than' => [
                'rule' => NumericRangeRule::createLessThan(3),
            ],
            'less than or equal to' => [
                'rule' => NumericRangeRule::createLessThanOrEqualTo(3),
            ],
            'between' => [
                'rule' => NumericRangeRule::createBetween(2, 4),
            ],
            'between with equal inclusive bounds' => [
                'rule' => NumericRangeRule::createBetween(3, 3),
            ],
            'between exclusive' => [
                'rule' => NumericRangeRule::createBetweenExclusive(2, 4),
            ],
            'between min exclusive' => [
                'rule' => NumericRangeRule::createBetweenMinExclusive(2, 4),
            ],
            'between max exclusive' => [
                'rule' => NumericRangeRule::createBetweenMaxExclusive(2, 4),
            ],
            'numeric string bounds' => [
                'rule' => NumericRangeRule::createBetween('2', '4'),
            ],
        ];
    }

    /**
     * @dataProvider getGreaterThanProvidedData
     * @param numeric $value
     * @param numeric $min
     */
    public function testGreaterThan(
        $value,
        $min,
        array $expectedResult
    ): void {
        self::assertActualResultEqualsExpected(
            NumericRangeRule::createGreaterThan($min)->validate(Context::create($value)),
            $expectedResult
        );
    }

    public static function getGreaterThanProvidedData(): array
    {
        return [
            'below boundary' => [
                'value' => 2,
                'min' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'equal to boundary' => [
                'value' => 3,
                'min' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'above boundary' => [
                'value' => 4,
                'min' => 3,
                'expectedResult' => self::validResult(),
            ],
            'value - float, min - int, just above boundary' => [
                'value' => 3.0001,
                'min' => 3,
                'expectedResult' => self::validResult(),
            ],
            'value - float, min - int, just below boundary' => [
                'value' => 2.9999,
                'min' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, min - float, equal to boundary' => [
                'value' => 3.1,
                'min' => 3.1,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, min - float, above boundary' => [
                'value' => 3.2,
                'min' => 3.1,
                'expectedResult' => self::validResult(),
            ],
            'value - int, min - float, above boundary' => [
                'value' => 4,
                'min' => 3.5,
                'expectedResult' => self::validResult(),
            ],
            'value - string, min - int, above boundary' => [
                'value' => '4',
                'min' => 3,
                'expectedResult' => self::validResult(),
            ],
            'value - string, min - int, equal to boundary' => [
                'value' => '3',
                'min' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, min - string, above boundary' => [
                'value' => '4',
                'min' => '3',
                'expectedResult' => self::validResult(),
            ],
            'value - string, min - string, equal to boundary' => [
                'value' => '3',
                'min' => '3',
                'expectedResult' => self::invalidResult(),
            ],
            'value - float string, min - int, above boundary' => [
                'value' => '3.5',
                'min' => 3,
                'expectedResult' => self::validResult(),
            ],
            'negative boundary' => [
                'value' => 0,
                'min' => -1,
                'expectedResult' => self::validResult(),
            ],
        ];
    }

    /**
     * @dataProvider getGreaterThanOrEqualToProvidedData
     * @param numeric $value
     * @param numeric $min
     */
    public function testGreaterThanOrEqualTo(
        $value,
        $min,
        array $expectedResult
    ): void {
        self::assertActualResultEqualsExpected(
            NumericRangeRule::createGreaterThanOrEqualTo($min)->validate(Context::create($value)),
            $expectedResult
        );
    }

    public static function getGreaterThanOrEqualToProvidedData(): array
    {
        return [
            'below boundary' => [
                'value' => 2,
                'min' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'int equal to boundary' => [
                'value' => 3,
                'min' => 3,
                'expectedResult' => self::validResult(),
            ],
            'value - float, min - float, equal to boundary' => [
                'value' => 3.1,
                'min' => 3.1,
                'expectedResult' => self::validResult(),
            ],
            'value - integer float, min - integer float, equal to boundary' => [
                'value' => 3.0,
                'min' => 3.0,
                'expectedResult' => self::validResult(),
            ],
            'value - int, min - float, equal to boundary' => [
                'value' => 3,
                'min' => 3.0,
                'expectedResult' => self::validResult(),
            ],
            'value - float, min - float, below boundary' => [
                'value' => 3.0,
                'min' => 3.1,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, min - float, above boundary' => [
                'value' => 3.2,
                'min' => 3.1,
                'expectedResult' => self::validResult(),
            ],
            'value - float, min - int, above boundary' => [
                'value' => 3.5,
                'min' => 3,
                'expectedResult' => self::validResult(),
            ],
            'value - string, min - int, equal to boundary' => [
                'value' => '3',
                'min' => 3,
                'expectedResult' => self::validResult(),
            ],
            'value - string, min - int, below boundary' => [
                'value' => '2',
                'min' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, min - string, equal to boundary' => [
                'value' => '3',
                'min' => '3',
                'expectedResult' => self::validResult(),
            ],
            'value - string, min - string, above boundary' => [
                'value' => '4',
                'min' => '3',
                'expectedResult' => self::validResult(),
            ],
            'value - float string, min - float, equal to boundary' => [
                'value' => '3.1',
                'min' => 3.1,
                'expectedResult' => self::validResult(),
            ],
            'above boundary' => [
                'value' => 4,
                'min' => 3,
                'expectedResult' => self::validResult(),
            ],
        ];
    }

    /**
     * @dataProvider getLessThanProvidedData
     * @param numeric $value
     * @param numeric $max
     */
    public function testLessThan(
        $value,
        $max,
        array $expectedResult
    ): void {
        self::assertActualResultEqualsExpected(
            NumericRangeRule::createLessThan($max)->validate(Context::create($value)),
            $expectedResult
        );
    }

    public static function getLessThanProvidedData(): array
    {
        return [
            'below boundary' => [
                'value' => 2,
                'max' => 3,
                'expectedResult' => self::validResult(),
            ],
            'equal to boundary' => [
                'value' => 3,
                'max' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'above boundary' => [
                'value' => 4,
                'max' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, max - int, just below boundary' => [
                'value' => 2.9999,
                'max' => 3,
                'expectedResult' => self::validResult(),
            ],
            'value - float, max - int, just above boundary' => [
                'value' => 3.0001,
                'max' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, max - float, equal to boundary' => [
                'value' => 3.1,
                'max' => 3.1,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, max - float, below boundary' => [
                'value' => 3.0,
                'max' => 3.1,
                'expectedResult' => self::validResult(),
            ],
            'value - int, max - float, below boundary' => [
                'value' => 3,
                'max' => 3.5,
                'expectedResult' => self::validResult(),
            ],
            'value - string, max - int, below boundary' => [
                'value' => '2',
                'max' => 3,
                'expectedResult' => self::validResult(),
            ],
            'value - string, max - int, equal to boundary' => [
                'value' => '3',
                'max' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, max - string, below boundary' => [
                'value' => '2',
                'max' => '3',
                'expectedResult' => self::validResult(),
            ],
            'value - string, max - string, equal to boundary' => [
                'value' => '3',
                'max' => '3',
                'expectedResult' => self::invalidResult(),
            ],
            'value - float string, max - int, below boundary' => [
                'value' => '2.5',
                'max' => 3,
                'expectedResult' => self::validResult(),
            ],
            'negative value below boundary' => [
                'value' => -5,
                'max' => 3,
                'expectedResult' => self::validResult(),
            ],
        ];
    }

    /**
     * @dataProvider getLessThanOrEqualToProvidedData
     * @param numeric $value
     * @param numeric $max
     */
    public function testLessThanOrEqualTo(
        $value,
        $max,
        array $expectedResult
    ): void {
        self::assertActualResultEqualsExpected(
            NumericRangeRule::createLessThanOrEqualTo($max)->validate(Context::create($value)),
            $expectedResult
        );
    }

    public static function getLessThanOrEqualToProvidedData(): array
    {
        return [
            'below boundary' => [
                'value' => 2,
                'max' => 3,
                'expectedResult' => self::validResult(),
            ],
            'equal to boundary' => [
                'value' => 3,
                'max' => 3,
                'expectedResult' => self::validResult(),
            ],
            'above boundary' => [
                'value' => 4,
                'max' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, max - float, equal to boundary' => [
                'value' => 3.1,
                'max' => 3.1,
                'expectedResult' => self::validResult(),
            ],
            'value - float, max - float, below boundary' => [
                'value' => 3.0,
                'max' => 3.1,
                'expectedResult' => self::validResult(),
            ],
            'value - float, max - float, above boundary' => [
                'value' => 3.2,
                'max' => 3.1,
                'expectedResult' => self::invalidResult(),
            ],
            'value - int, max - float, below boundary' => [
                'value' => 3,
                'max' => 3.5,
                'expectedResult' => self::validResult(),
            ],
            'value - float, max - int, above boundary' => [
                'value' => 3.5,
                'max' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, max - int, equal to boundary' => [
                'value' => '3',
                'max' => 3,
                'expectedResult' => self::validResult(),
            ],
            'value - string, max - int, above boundary' => [
                'value' => '4',
                'max' => 3,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, max - string, equal to boundary' => [
                'value' => '3',
                'max' => '3',
                'expectedResult' => self::validResult(),
            ],
            'value - string, max - string, below boundary' => [
                'value' => '2',
                'max' => '3',
                'expectedResult' => self::validResult(),
            ],
            'value - float string, max - float, equal to boundary' => [
                'value' => '3.1',
                'max' => 3.1,
                'expectedResult' => self::validResult(),
            ],
        ];
    }

    /**
     * @dataProvider getBetweenProvidedData
     * @param numeric $value
     * @param numeric $min
     * @param numeric $max
     */
    public function testBetween(
        $value,
        $min,
        $max,
        array $expectedResult
    ): void {
        self::assertActualResultEqualsExpected(
            NumericRangeRule::createBetween($min, $max)->validate(Context::create($value)),
            $expectedResult
        );
    }

    public static function getBetweenProvidedData(): array
    {
        return [
            'below min' => [
                'value' => 1,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'equal to min' => [
                'value' => 2,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'inside range' => [
                'value' => 3,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'equal to max' => [
                'value' => 4,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'above max' => [
                'value' => 5,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - int, inside range' => [
                'value' => 3.5,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - int, below min' => [
                'value' => 1.9999,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - int, above max' => [
                'value' => 4.0001,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - float, equal to min' => [
                'value' => 2.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - float, equal to max' => [
                'value' => 4.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - float, inside range' => [
                'value' => 3.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::validResult(),
            ],
            'value - int, bounds - float, below min' => [
                'value' => 2,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, bounds - int, inside range' => [
                'value' => '3',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - int, equal to min' => [
                'value' => '2',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - int, above max' => [
                'value' => '5',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, bounds - string, inside range' => [
                'value' => '3',
                'min' => '2',
                'max' => '4',
                'expectedResult' => self::validResult(),
            ],
            'value - float string, bounds - int, inside range' => [
                'value' => '3.5',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'equal to single point range' => [
                'value' => 3,
                'min' => 3,
                'max' => 3,
                'expectedResult' => self::validResult(),
            ],
        ];
    }

    /**
     * @dataProvider getBetweenExclusiveProvidedData
     * @param numeric $value
     * @param numeric $min
     * @param numeric $max
     */
    public function testBetweenExclusive(
        $value,
        $min,
        $max,
        array $expectedResult
    ): void {
        self::assertActualResultEqualsExpected(
            NumericRangeRule::createBetweenExclusive($min, $max)->validate(Context::create($value)),
            $expectedResult
        );
    }

    public static function getBetweenExclusiveProvidedData(): array
    {
        return [
            'equal to min' => [
                'value' => 2,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'inside range' => [
                'value' => 3,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'equal to max' => [
                'value' => 4,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - int, inside range' => [
                'value' => 3.5,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - int, just above min' => [
                'value' => 2.0001,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - int, just below max' => [
                'value' => 3.9999,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - float, equal to min' => [
                'value' => 2.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - float, equal to max' => [
                'value' => 4.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - float, inside range' => [
                'value' => 3.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::validResult(),
            ],
            'value - int, bounds - float, inside range' => [
                'value' => 3,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - int, inside range' => [
                'value' => '3',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - int, equal to min' => [
                'value' => '2',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, bounds - int, equal to max' => [
                'value' => '4',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, bounds - string, inside range' => [
                'value' => '3',
                'min' => '2',
                'max' => '4',
                'expectedResult' => self::validResult(),
            ],
            'value - float string, bounds - int, inside range' => [
                'value' => '3.5',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
        ];
    }

    /**
     * @dataProvider getBetweenMinExclusiveProvidedData
     * @param numeric $value
     * @param numeric $min
     * @param numeric $max
     */
    public function testBetweenMinExclusive(
        $value,
        $min,
        $max,
        array $expectedResult
    ): void {
        self::assertActualResultEqualsExpected(
            NumericRangeRule::createBetweenMinExclusive($min, $max)->validate(Context::create($value)),
            $expectedResult
        );
    }

    public static function getBetweenMinExclusiveProvidedData(): array
    {
        return [
            'equal to min' => [
                'value' => 2,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'inside range' => [
                'value' => 3,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'equal to max' => [
                'value' => 4,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - int, inside range' => [
                'value' => 3.5,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - int, just above min' => [
                'value' => 2.0001,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - int, just below min' => [
                'value' => 1.9999,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - int, above max' => [
                'value' => 4.0001,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - float, equal to min' => [
                'value' => 2.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - float, equal to max' => [
                'value' => 4.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::validResult(),
            ],
            'value - int, bounds - float, inside range' => [
                'value' => 3,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - int, inside range' => [
                'value' => '3',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - int, equal to min' => [
                'value' => '2',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, bounds - int, equal to max' => [
                'value' => '4',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - string, inside range' => [
                'value' => '3',
                'min' => '2',
                'max' => '4',
                'expectedResult' => self::validResult(),
            ],
            'value - float string, bounds - int, inside range' => [
                'value' => '3.5',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
        ];
    }

    /**
     * @dataProvider getBetweenMaxExclusiveProvidedData
     * @param numeric $value
     * @param numeric $min
     * @param numeric $max
     */
    public function testBetweenMaxExclusive(
        $value,
        $min,
        $max,
        array $expectedResult
    ): void {
        self::assertActualResultEqualsExpected(
            NumericRangeRule::createBetweenMaxExclusive($min, $max)->validate(Context::create($value)),
            $expectedResult
        );
    }

    public static function getBetweenMaxExclusiveProvidedData(): array
    {
        return [
            'equal to min' => [
                'value' => 2,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'inside range' => [
                'value' => 3,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'equal to max' => [
                'value' => 4,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - int, inside range' => [
                'value' => 3.5,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - int, just below max' => [
                'value' => 3.9999,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - int, just above max' => [
                'value' => 4.0001,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - int, below min' => [
                'value' => 1.9999,
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - float, bounds - float, equal to min' => [
                'value' => 2.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::validResult(),
            ],
            'value - float, bounds - float, equal to max' => [
                'value' => 4.5,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::invalidResult(),
            ],
            'value - int, bounds - float, inside range' => [
                'value' => 3,
                'min' => 2.5,
                'max' => 4.5,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - int, inside range' => [
                'value' => '3',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - int, equal to min' => [
                'value' => '2',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
            'value - string, bounds - int, equal to max' => [
                'value' => '4',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::invalidResult(),
            ],
            'value - string, bounds - string, inside range' => [
                'value' => '3',
                'min' => '2',
                'max' => '4',
                'expectedResult' => self::validResult(),
            ],
            'value - float string, bounds - int, inside range' => [
                'value' => '3.5',
                'min' => 2,
                'max' => 4,
                'expectedResult' => self::validResult(),
            ],
        ];
    }

    /**
     * @dataProvider getInvalidContextProvidedData
     * @param mixed $value
     */
    public function testThrowsInvalidRuleContextException($value): void
    {
        $this->expectException(InvalidRuleContextException::class);

        NumericRangeRule::createGreaterThanOrEqualTo(3)->validate(Context::create($value));
    }

    public static function getInvalidContextProvidedData(): array
    {
        return [
            'context value - true' => [
                'value' => true,
            ],
            'context value - false' => [
                'value' => false,
            ],
            'context value - null' => [
                'value' => null,
            ],
            'context value - empty string' => [
                'value' => '',
            ],
            'context value - non numeric string' => [
                'value' => 'abc',
            ],
            'context value - not empty array' => [
                'value' => [123,],
            ],
            'context value - empty array' => [
                'value' => [],
            ],
            'context value - object' => [
                'value' => new stdClass(),
            ],
            'context value - callable' => [
                'value' => static function () {
                },
            ],
            'context value - resource' => [
                'value' => tmpfile(),
            ],
        ];
    }

    /**
     * @dataProvider getInvalidConfigurationProvidedData
     */
    public function testFactoryThrowsInvalidRuleContextException(callable $factory): void
    {
        $this->expectException(InvalidRuleContextException::class);

        $factory();
    }

    public static function getInvalidConfigurationProvidedData(): array
    {
        return [
            'between with min greater than max' => [
                'factory' => static function () {
                    NumericRangeRule::createBetween(4, 2);
                },
            ],
            'between exclusive with min greater than max' => [
                'factory' => static function () {
                    NumericRangeRule::createBetweenExclusive(4, 2);
                },
            ],
            'between exclusive with equal bounds' => [
                'factory' => static function () {
                    NumericRangeRule::createBetweenExclusive(3, 3);
                },
            ],
            'between min exclusive with equal bounds' => [
                'factory' => static function () {
                    NumericRangeRule::createBetweenMinExclusive(3, 3);
                },
            ],
            'between max exclusive with equal bounds' => [
                'factory' => static function () {
                    NumericRangeRule::createBetweenMaxExclusive(3, 3);
                },
            ],
            'greater than with non numeric bound' => [
                'factory' => static function () {
                    NumericRangeRule::createGreaterThan('abc');
                },
            ],
            'less than with non numeric bound' => [
                'factory' => static function () {
                    NumericRangeRule::createLessThan('abc');
                },
            ],
            'between with non numeric bounds' => [
                'factory' => static function () {
                    NumericRangeRule::createBetween('a', 'b');
                },
            ],
        ];
    }

    private static function validResult(): array
    {
        return [
            'isValid' => true,
            'failedRuleCode' => null,
        ];
    }

    private static function invalidResult(): array
    {
        return [
            'isValid' => false,
            'failedRuleCode' => 'numeric_range_rule',
        ];
    }

    private static function assertActualResultEqualsExpected(
        Result $result,
        array $expectedResult
    ): void {
        self::assertEquals($expectedResult['isValid'], $result->isValid());
        self::assertEquals($expectedResult['failedRuleCode'], $result->getFailedRuleCode());
    }
}
