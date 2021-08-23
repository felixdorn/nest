<?php

namespace Felix\Nest\Support;

use Exception;

class TimeUnit
{
    public const SECOND = 1;
    public const MINUTE = self::SECOND * 60;
    public const HOUR   = self::MINUTE * 60;
    public const DAY    = self::HOUR * 24;
    public const WEEK   = self::DAY * 7;

    public const NAMES = [
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

        's' => self::SECOND,
        'm' => self::MINUTE,
        'h' => self::HOUR,
        'd' => self::DAY,
        'w' => self::WEEK,
    ];

    public static function convert(string $unit): int
    {
        // TODO: custom exception
        return self::NAMES[$unit] ?? throw new Exception('invalid time unit: ' . $unit);
    }
}
