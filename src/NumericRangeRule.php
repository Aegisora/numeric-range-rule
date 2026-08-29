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
}
