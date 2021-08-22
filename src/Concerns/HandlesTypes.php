<?php

namespace Felix\StructuredTime\Concerns;

use Felix\StructuredTime\Exceptions\InvalidTimeUnitException;
use Felix\StructuredTime\Symbols;
use Felix\StructuredTime\TimeUnit;

trait HandlesTypes
{
    public function isWeekday(string $value): bool
    {
        return array_key_exists($value, Symbols::DAYS_OF_THE_WEEK);
    }

    public function isDate(string $value): bool
    {
        return preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/', $value);
    }

    public function isTime(string $value): bool
    {
        return preg_match('/^[0-9]{1,2}:[0-9]{1,2}(|:[0-9]{1,2})$/', $value);
    }

    public function isNumber(string $value): bool
    {
        return preg_match('/^[0-9]+$/', $value);
    }

    public function isTimeUnit(string $value): bool
    {
        try {
            TimeUnit::convert($value);
            return true;
        } catch (InvalidTimeUnitException) {
            return false;
        }
    }
}
