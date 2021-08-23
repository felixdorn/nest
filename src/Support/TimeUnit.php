<?php

namespace Felix\Nest\Support;

use Felix\Nest\Exceptions\InvalidTimeUnitException;

class TimeUnit
{
    public const SECOND = 1;
    public const MINUTE = self::SECOND * 60;
    public const HOUR   = self::MINUTE * 60;
    public const DAY    = self::HOUR * 24;
    public const WEEK   = self::DAY * 7;

    public const NAMES = [
        's' => self::SECOND,
        'm' => self::MINUTE,
        'h' => self::HOUR,
        'd' => self::DAY,
        'w' => self::WEEK,

        'second' => self::SECOND,
        'minute' => self::MINUTE,
        'hour'   => self::HOUR,
        'day'    => self::DAY,
        'week'   => self::WEEK,

        'seconds' => self::SECOND,
        'minutes' => self::MINUTE,
        'hours'   => self::HOUR,
        'days'    => self::DAY,
        'weeks'   => self::WEEK,
    ];

    public static function convert(string $unit): int
    {
        return self::NAMES[$unit] ?? throw new InvalidTimeUnitException('invalid time unit: ' . $unit);
    }
}
