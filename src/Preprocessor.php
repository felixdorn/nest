<?php

namespace Felix\Nest;

use Carbon\CarbonInterface;
use Felix\Nest\Support\TimeUnit;

class Preprocessor
{
    protected array $numbers = [
        'one'          => 1,
        'two'          => 2,
        'three'        => 3,
        'four'         => 4,
        'five'         => 5,
        'six'          => 6,
        'seven'        => 7,
        'eight'        => 8,
        'nine'         => 9,
        'ten'          => 10,
        'eleven'       => 11,
        'twelve'       => 12,
        'thirteen'     => 13,
        'fourteen'     => 14,
        'fifteen'      => 15,
        'sixteen'      => 16,
        'seventeen'    => 17,
        'eighteen'     => 18,
        'nineteen'     => 19,
        'twenty'       => 20,
        'twenty-one'   => 21,
        'twenty-two'   => 22,
        'twenty-three' => 23,
        'twenty-four'  => 24,
        'twenty-five'  => 25,
        'twenty-six'   => 26,
        'twenty-seven' => 27,
        'twenty-eight' => 28,
        'twenty-nine'  => 29,
        'thirty'       => 30,
        'thirty-one'   => 31,
        'thirty-two'   => 32,
        'thirty-three' => 33,
        'thirty-four'  => 34,
        'thirty-five'  => 35,
        'thirty-six'   => 36,
        'thirty-seven' => 37,
        'thirty-eight' => 38,
        'thirty-nine'  => 39,
        'forty'        => 40,
        'forty-one'    => 41,
        'forty-two'    => 42,
        'forty-three'  => 43,
        'forty-four'   => 44,
        'forty-five'   => 45,
        'forty-six'    => 46,
        'forty-seven'  => 47,
        'forty-eight'  => 48,
        'forty-nine'   => 49,
        'fifty'        => 50,
        'fifty-one'    => 51,
        'fifty-two'    => 52,
        'fifty-three'  => 53,
        'fifty-four'   => 54,
        'fifty-five'   => 55,
        'fifty-six'    => 56,
        'fifty-seven'  => 57,
        'fifty-eight'  => 58,
        'fifty-nine'   => 59,
        'sixty'        => 60,
    ];

    public function preprocess(string $code, CarbonInterface $current): string
    {
        $elements = array_filter(
            explode(' ', trim($code)),
            fn ($element) => trim($element) !== ''
        );

        foreach ($elements as $k => $element) {
            $previous = $elements[$k - 1] ?? '';

            $elements[$k] = $this->extractDates($element, $current);

            if ($previous === 'at' || $previous === 'from' || $previous === 'to') {
                $elements[$k] = $this->extractTime($elements[$k]);
                continue;
            }

            $elements[$k] = $this->expandShorthands($elements[$k], $current);
        }

        return implode(' ', $elements);
    }

    protected function extractDates(string $element, CarbonInterface $current): string
    {
        $element = preg_replace('/^(\d{1,2})\/(\d{1,2})\/(\d{1,4})$/', '$3-$2-$1', $element) ?: $element;

        preg_match('/^\d{1,4}-\d{1,2}-\d{1,2}$/', $element, $matches);

        if (count($matches) === 0) {
            return $element;
        }
        $date = $matches[0];

        [$year, $month, $day] = explode('-', $date);

        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $day   = str_pad($day, 2, '0', STR_PAD_LEFT);

        if (strlen($year) === 2) {
            $year = ($current->millennium - 1) . '0' . $year;
        }

        if (strlen($year) === 1) {
            $year = ($current->millennium - 1) . '00' . $year;
        }

        return sprintf('%s-%s-%s', $year, $month, $day);
    }

    protected function extractTime(string $element): string
    {
        preg_match('/^\d{1,2}(:\d{1,2}|)(am|pm|PM|AM|)$/', $element, $matches);

        if (count($matches) === 0) {
            return $element;
        }

        // So AM/PM becomes am/pm
        $format = strtolower($matches[2]);

        // Time is 12-hour format
        if ($format !== '') {
            $element = substr($element, 0, -2); // removes the AM/PM
        }

        if (!str_contains($element, ':')) {
            $element .= ':00';
        }

        [$hours, $minutes] = explode(':', $element);
        $hours             = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $minutes           = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        // Time is in 12-hour format
        if ($format !== '' && $hours === '12') {
            $hours = '00';
        }

        if ($format === 'pm') {
            $hours = (int) $hours + 12;
        }

        return $hours . ':' . $minutes;
    }

    protected function expandShorthands(string $element, CarbonInterface $now): string
    {
        if ($element === 'everyday') {
            return 'every monday, tuesday, wednesday, thursday, friday, saturday, sunday';
        }

        if ($element === 'an' || $element === 'a') {
            return '1';
        }

        if ($element === 'tomorrow') {
            return $now->clone()->addDay()->toDateString();
        }

        if ($element === 'weekend' || $element === 'week-end') {
            return 'saturday and sunday';
        }

        if (preg_match('/(?<measure>[0-9]+)(?<unit>[a-zA-Z])/', $element, $matches)) {
            $measure = $matches['measure'];
            $unit    = $matches['unit'];

            return sprintf('%s %s', $measure, TimeUnit::expand($unit));
        }

        return $this->numbers[$element] ?? $element;
    }
}
