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
    ];

    public const ABBREVIATIONS = [
        's' => self::NAMES['second'],
        'm' => self::NAMES['minute'],
        'h' => self::NAMES['hour'],
        'd' => self::NAMES['day'],
        'w' => self::NAMES['week'],
    ];

    public static function convert(string $unit): int
    {
        return self::NAMES[$unit] ??
            self::ABBREVIATIONS[$unit] ??
            // TODO: custom exception
            throw new Exception('invalid time unit: ' . $unit);
    }
}
