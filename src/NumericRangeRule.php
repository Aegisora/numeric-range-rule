<?php

namespace Aegisora\Rules;

class NumericRangeRule
{
    /**
     * @var numeric|null
     */
    private $min;

    /**
     * @var numeric|null
     */
    private ?int $max;

    private bool $minInclusive;
    private bool $maxInclusive;

    /**
     * @param numeric|null $min
     * @param numeric|null $max
     * @param bool $minInclusive
     * @param bool $maxInclusive
     */
    private function __construct(
        $min,
        $max,
        bool $minInclusive,
        bool $maxInclusive
    ) {
        $this->min = $min;
        $this->max = $max;
        $this->minInclusive = $minInclusive;
        $this->maxInclusive = $maxInclusive;
    }

    /**
     * @param numeric $length
     */
    public static function createGreaterThan($length): self
    {
        return new self($length, null, false, false);
    }

    /**
     * @param numeric $length
     */
    public static function createGreaterThanOrEqualTo($length): self
    {
        return new self($length, null, true, false);
    }

    /**
     * @param numeric $length
     */
    public static function createLessThan($length): self
    {
        return new self(null, $length, false, false);
    }

    /**
     * @param numeric $length
     */
    public static function createLessThanOrEqualTo($length): self
    {
        return new self(null, $length, false, true);
    }

    /**
     * @param numeric $min
     * @param numeric $max
     */
    public static function createBetween(
        int $min,
        int $max
    ): self {
        return new self($min, $max, true, true);
    }

    /**
     * @param numeric $min
     * @param numeric $max
     */
    public static function createBetweenExclusive(
        $min,
        $max
    ): self {
        return new self($min, $max, false, false);
    }
}
