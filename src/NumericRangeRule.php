<?php

namespace Aegisora\Rules;

use Aegisora\RuleContract\Exceptions\InvalidRuleContextException;
use Aegisora\RuleContract\Models\Context;
use Aegisora\RuleContract\Models\Result;
use Aegisora\RuleContract\Rule;

class NumericRangeRule extends Rule
{
    /**
     * @var numeric|null
     */
    private $min;

    /**
     * @var numeric|null
     */
    private $max;

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
        $min,
        $max
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

    /**
     * @param numeric $min
     * @param numeric $max
     */
    public static function createBetweenMinExclusive(
        $min,
        $max
    ): self {
        return new self($min, $max, false, true);
    }

    /**
     * @param numeric $min
     * @param numeric $max
     */
    public static function createBetweenMaxExclusive(
        $min,
        $max
    ): self {
        return new self($min, $max, true, false);
    }

    protected function executeValidate(Context $context): Result
    {
        $value = $context->getValue();

        if (!is_numeric($value)) {
            throw new InvalidRuleContextException();
        }

        $this->validateRangeValues();

        return $this->isSatisfiedBy($value) ?
            $this->getDefaultValidResult() :
            $this->getDefaultInvalidResult();
    }

    /**
     * @param numeric $length
     */
    private function isSatisfiedBy($length): bool
    {
        if (!is_null($this->min)) {
            $satisfiesMin = $this->minInclusive ? ($length >= $this->min) : ($length > $this->min);

            if (!$satisfiesMin) {
                return false;
            }
        }

        if (!is_null($this->max)) {
            $satisfiesMax = $this->maxInclusive ? ($length <= $this->max) : ($length < $this->max);

            if (!$satisfiesMax) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws InvalidRuleContextException
     */
    private function validateRangeValues(): void
    {
        if ($this->min !== null) {
            if (!is_numeric($this->min)) {
                throw new InvalidRuleContextException();
            }
        }

        if ($this->max !== null) {
            if (!is_numeric($this->max)) {
                throw new InvalidRuleContextException();
            }
        }
    }
}
