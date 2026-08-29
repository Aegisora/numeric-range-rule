# Aegisora Numeric Range Rule

[![Latest Version](https://img.shields.io/packagist/v/aegisora/numeric-range-rule?style=flat-square)](https://packagist.org/packages/aegisora/numeric-range-rule)
[![Total Downloads](https://img.shields.io/packagist/dt/aegisora/numeric-range-rule?style=flat-square)](https://packagist.org/packages/aegisora/numeric-range-rule)
![Code Coverage Badge](./badge.svg)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHPStan Badge](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)

Numeric Range Rule provides a simple, rule-based numeric range validation implementation for the Aegisora ecosystem.

It is built on top of [`aegisora/rule-contract`](https://github.com/Aegisora/rule-contract) and follows its strict validation architecture, ensuring consistent and predictable behavior across applications.

This rule is useful for validating user input, form fields, prices, quantities, ages, percentages, API request parameters, configuration values, and any other numeric value that must satisfy a range boundary.

---

## 📑 Table of Contents
- [Features](#-features)
- [Installation](#-installation)
- [Core Concept](#-core-concept)
- [Basic Usage](#-basic-usage)
- [Valid vs Invalid](#-valid-vs-invalid)
- [Validation Result](#-validation-result)
- [Guardian Usage](#-guardian-usage)
- [Real-World Examples](#-real-world-examples)
- [Factory Methods](#-factory-methods)
- [Architecture](#-architecture)
- [License](#-license)
- [Contributing](#-contributing)
- [Support](#-support)

---

## ✨ Features
- 🔹 Lightweight and dependency-free except `aegisora/rule-contract`
- 🔹 Validates a numeric value against a lower bound, an upper bound, or a range
- 🔹 Supports strict (`>`, `<`) and inclusive (`>=`, `<=`) comparisons
- 🔹 Accepts integers, floats, and numeric strings via native `is_numeric()`
- 🔹 Rejects non-numeric input as an invalid context
- 🔹 Rejects an impossible range configuration (e.g. `min > max`) at construction time
- 🔹 Fully compatible with Aegisora validation pipeline
- 🔹 Strict `Context` → `Result` validation flow
- 🔹 No raw booleans — only structured results
- 🔹 Safe execution via base `Rule` abstraction
- 🔹 Expressive factory API for every boundary variation
- 🔹 Ready to use out of the box

---

## 📦 Installation

```bash
composer require aegisora/numeric-range-rule
```

---

## 🚀 Core Concept

This package implements a single validation rule with several factory variations:

- accepts a numeric value via `Context`
- checks whether the value satisfies the configured boundary
- returns a standardized `Result`

Under the hood it wraps the common boilerplate:

```php
if ($value < $min || $value > $max) {
    // value is out of the allowed boundary
}
```

into a reusable rule that reports its outcome through a `Result` object instead of a raw boolean.

---

## 🏗️ Basic Usage

```php
use Aegisora\RuleContract\Models\Context;
use Aegisora\Rules\NumericRangeRule;

$result = NumericRangeRule::createBetween(1, 100)->validate(Context::create(42));

if ($result->isValid()) {
    // value satisfies the boundary
} else {
    // value is out of the allowed boundary
}
```

---

## ✅ Valid vs Invalid

The rule passes when the numeric value satisfies the configured boundary and fails otherwise. Integers, floats, and numeric strings are all accepted as input.

### Lower bound

```php
NumericRangeRule::createGreaterThan(3)->validate(Context::create(4));         // valid   — 4 > 3
NumericRangeRule::createGreaterThan(3)->validate(Context::create(3));         // invalid — 3 is not > 3

NumericRangeRule::createGreaterThanOrEqualTo(3)->validate(Context::create(3)); // valid   — 3 >= 3
NumericRangeRule::createGreaterThanOrEqualTo(3)->validate(Context::create(2)); // invalid — 2 < 3
```

### Upper bound

```php
NumericRangeRule::createLessThan(3)->validate(Context::create(2));            // valid   — 2 < 3
NumericRangeRule::createLessThan(3)->validate(Context::create(3));            // invalid — 3 is not < 3

NumericRangeRule::createLessThanOrEqualTo(3)->validate(Context::create(3));   // valid   — 3 <= 3
NumericRangeRule::createLessThanOrEqualTo(3)->validate(Context::create(4));   // invalid — 4 > 3
```

### Range

```php
NumericRangeRule::createBetween(2, 4)->validate(Context::create(3));          // valid   — 2 <= 3 <= 4
NumericRangeRule::createBetween(2, 4)->validate(Context::create(1));          // invalid — 1 < 2

NumericRangeRule::createBetweenExclusive(2, 4)->validate(Context::create(3)); // valid   — 2 < 3 < 4
NumericRangeRule::createBetweenExclusive(2, 4)->validate(Context::create(2)); // invalid — 2 is not > 2

NumericRangeRule::createBetweenMinExclusive(2, 4)->validate(Context::create(4)); // valid    — 2 < 4 <= 4
NumericRangeRule::createBetweenMinExclusive(2, 4)->validate(Context::create(2)); // invalid  — 2 is not > 2

NumericRangeRule::createBetweenMaxExclusive(2, 4)->validate(Context::create(2)); // valid    — 2 <= 2 < 4
NumericRangeRule::createBetweenMaxExclusive(2, 4)->validate(Context::create(4)); // invalid  — 4 is not < 4
```

---

## 🧪 Validation Result

If the value satisfies the boundary, the rule returns a valid result.

`$result->isValid(); // true`

If the value is out of the boundary, the rule returns an invalid result.

```php
$result->isValid(); // false
$result->getFailedRuleCode(); // numeric_range_rule
```

If the context value is not numeric, the rule throws:

`Aegisora\RuleContract\Exceptions\InvalidRuleContextException`

The same exception is thrown at construction time when the range configuration is impossible, e.g. `NumericRangeRule::createBetween(4, 2)` (`$min > $max`) or an empty exclusive range such as `NumericRangeRule::createBetweenExclusive(3, 3)`.

---

## 🔗 Guardian Usage

This rule can be used together with `aegisora/guardian` to build fluent validation pipelines.

```php
use Aegisora\Guardian\Guardian;
use Aegisora\Rules\NumericRangeRule;
use App\Exceptions\InvalidAgeException;

$guardian = new Guardian();

$guardian
    ->that($age)
    ->must(NumericRangeRule::createBetween(18, 120), new InvalidAgeException())
    ->validate();
```

If the value is out of the allowed boundary, `Guardian` throws the provided domain exception.

---

## 🧭 Real-World Examples

Numeric Range Rule is useful for enforcing range constraints before values are persisted or processed.

Examples

```text
User Registration:

require an age between 18 and 120
```
```text
E-commerce:

require a quantity of at least 1
```
```text
Configuration:

ensure a percentage stays between 0 and 100
```
```text
API:

reject request parameters that fall outside an allowed range
```

---

## 🧩 Factory Methods
`NumericRangeRule::createGreaterThan($min);`
- passes when the value is strictly greater than `$min`

`NumericRangeRule::createGreaterThanOrEqualTo($min);`
- passes when the value is greater than or equal to `$min`

`NumericRangeRule::createLessThan($max);`
- passes when the value is strictly less than `$max`

`NumericRangeRule::createLessThanOrEqualTo($max);`
- passes when the value is less than or equal to `$max`

`NumericRangeRule::createBetween($min, $max);`
- passes when the value is between `$min` and `$max`, both boundaries inclusive (`$min <= value <= $max`)

`NumericRangeRule::createBetweenExclusive($min, $max);`
- passes when the value is between `$min` and `$max`, both boundaries exclusive (`$min < value < $max`)

`NumericRangeRule::createBetweenMinExclusive($min, $max);`
- passes when the value is between `$min` (exclusive) and `$max` (inclusive) (`$min < value <= $max`)

`NumericRangeRule::createBetweenMaxExclusive($min, $max);`
- passes when the value is between `$min` (inclusive) and `$max` (exclusive) (`$min <= value < $max`)

`NumericRangeRule::createBetween($min, $max)->validate($context);`
- `$context` — `Context` wrapping the numeric value to validate

---

## 🏛️ Architecture

This package relies on [`aegisora/rule-contract`](https://github.com/Aegisora/rule-contract).

Flow:
1. `validate()` is called
2. `Context` is passed in
3. The value is extracted from context (non-numeric values raise `InvalidRuleContextException`)
4. The value is compared against the configured boundary
5. `Result` is returned — valid on success, invalid with the `numeric_range_rule` code on failure

All logic is safely handled by Rule contract.

---

## ⚖️ License

This package is open-source and licensed under the MIT License. See the [LICENSE](LICENSE) for details.

---

## 🌱 Contributing

Contributions are welcome and greatly appreciated! See the [CONTRIBUTING](CONTRIBUTING.md) for details.

---

## 🌟 Support

If you find this project useful, please consider giving it a star on GitHub!

It helps the project grow and motivates further development.
