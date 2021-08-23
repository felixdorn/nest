<?php

namespace Felix\Nest;

use Carbon\CarbonInterface;
use Exception;

class Preprocessor
{
    protected array $symbols = [];

    public function __construct()
    {
    }

    public function preprocess(string $code, CarbonInterface $current): Code
    {
        if (str_contains($code, '$')) {
            // TODO: Yeah, make that better.
            throw new Exception('compile error: code can not contain $ signs.');
        }

        $elements = explode(' ', strtolower($code));

        foreach ($elements as $k => $element) {
            $elements[$k] = $this->extractDates($element, $current);
            $elements[$k] = $this->extractTime($elements[$k]);
            $elements[$k] = $this->expandShorthands($elements[$k]);
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
        preg_match('/^\d{1,2}(:\d{1,2}|)$/', $element, $matches);

        if (count($matches) === 0) {
            return $element;
        }

        if (!str_contains($element, ':')) {
            $element .= ':00';
        }

        [$hours, $minutes] = explode(':', $element);
        $hours             = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $minutes           = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        $this->symbols[] = $hours . ':' . $minutes;

        return '$' . array_key_last($this->symbols);
    }

    protected function expandShorthands(string $element): string
    {
        if ($element === 'everyday') {
            return 'every monday, tuesday, wednesday, thursday, friday, saturday, sunday';
        }

        if ($element === 'an' || $element === 'a') {
            return '1';
        }

        return $element;
    }
}
