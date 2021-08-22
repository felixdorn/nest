<?php

namespace Felix\StructuredTime\Concerns;

use Felix\StructuredTime\Exceptions\InvalidTimeUnitException;
use Felix\StructuredTime\Support\TimeUnit;

trait HandlesTypes
{
    public array $weekDays = [
        'monday'    => 0,
        'tuesday'   => 1,
        'wednesday' => 2,
        'thursday'  => 3,
        'friday'    => 4,
        'saturday'  => 5,
        'sunday'    => 6,
    ];

    public function isWeekday(string $value): bool
    {
        return array_key_exists($value, $this->weekDays);
    }

    public function isDate(string $value): bool
    {
        return preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/', $value);
    }

    public function isTime(string $value): bool
    {
        return preg_match('/^[0-9]:[0-9]{1,2}(AM|PM)$/', $value);
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
