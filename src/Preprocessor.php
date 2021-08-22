<?php

namespace Felix\Nest;

use Carbon\CarbonInterface;

class Preprocessor
{
    protected array $symbols = [];

    public function __construct()
    {
    }

    public function preprocess(string $code, CarbonInterface $current): Code
    {
        $elements = explode(' ', strtolower($code));

        foreach ($elements as $k => $element) {
            $elements[$k] = $this->extractDates($element, $current);
            $elements[$k] = $this->extractTime($elements[$k]);
        }

        return new Code(
            implode(' ', $elements),
            $this->symbols
        );
    }

    protected function extractDates(string $element, CarbonInterface $current): string
    {
        preg_match('/^\d{1,2}\/\d{1,2}\/\d{1,4}$/', $element, $matches);

        if (count($matches) === 0) {
            return $element;
        }
        $date = $matches[0];

        [$month, $day, $year] = explode('/', $date);

        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $day   = str_pad($day, 2, '0', STR_PAD_LEFT);

        if (strlen($year) === 2) {
            $year = ($current->millennium - 1) . '0' . $year;
        }

        if (strlen($year) === 1) {
            $year = ($current->millennium - 1) . '00' . $year;
        }

        $this->symbols[] = sprintf('%s/%s/%s', $month, $day, $year);

        return '$' . array_key_last($this->symbols);
    }

    protected function extractTime(string $element): string
    {
        preg_match_all('/^\d{1,2}(:\d{1,2}|)(am|pm|)$/', $element, $matches);

        if (count($matches[0]) === 0) {
            return $element;
        }

        $time = $matches[0][0];

        if (!str_ends_with($time, 'am') && !str_ends_with($time, 'pm')) {
            $time .= 'pm';
        }

        $period = $time[-2] . 'm';
        $time   = substr($time, 0, -2);

        $time = ltrim($time, '0');

        // Time was "0"
        if ($time === '') {
            $time = '00:00';
        }

        // Time was 0AM/PM
        if ($time === 'am' || $time === 'pm') {
            $time = '00:00';
        }

        // Time was "00:x"
        if (str_starts_with($time, ':')) {
            $time = '00' . $time;
        }

        // Time was "x:00"
        if (str_ends_with($time, ':')) {
            $time .= '00';
        }

        // 12am becomes 12:00am
        $time              = preg_replace('/^(\d{1,2})$/', '$1:00', $time);
        [$hours, $minutes] = explode(':', $time ?? ':');

        $hours   = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        $this->symbols[] = $hours . ':' . $minutes . $period;

        return '$' . array_key_last($this->symbols);
    }
}
