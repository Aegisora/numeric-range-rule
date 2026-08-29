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
}
