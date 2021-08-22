<?php

namespace Felix\Nest;

class Preprocessor
{
    protected array $code;
    protected array $symbols = [];

    public function __construct(string $code)
    {
        $this->code = explode(' ', strtolower($code));
    }

    public function preprocess(): Code
    {
        foreach ($this->code as $k => $element) {
            $this->code[$k] = $this->extractDates($element);
            $this->code[$k] = $this->extractTime($this->code[$k]);
            $this->code[$k] = $this->extractLiteralTime($this->code[$k], $k);
        }

        return new Code(
            implode(' ', $this->code),
            $this->symbols
        );
    }

    protected function extractDates(string $element): string
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
            // TODO: Here get the current century from a global context
            $year = '20' . $year;
        }

        if (strlen($year) === 1) {
            // TODO: Here get the current century from a global context
            $year = '200' . $year;
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
        [$hours, $minutes] = explode(':', $time);

        $hours   = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        $this->symbols[] = $hours . ':' . $minutes . $period;

        return '$' . array_key_last($this->symbols);
    }
}
