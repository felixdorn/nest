<?php

namespace Felix\Nest;

use DateTime;
use DateTimeInterface;
use Felix\Nest\Support\TimeUnit;

class Lexer
{
    public const KEYWORDS = ['every', 'for', 'between', 'until', 'at', 'from', 'once'];

    public function tokenize(string $code): Event
    {
        $walker = new Walker($code);
        $event  = new Event();

        while (!$walker->eof()) {
            if ($walker->current() === '"') {
                // Skipping the starting quote
                $walker->next();
                $event->label = $walker->takeUntil('"');
                // Skipping the closing quote
                $walker->next();
                continue;
            }

            $keyword = $walker->takeUntil(' ');

            // Implicit once keyword
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $keyword)) {
                $event->when = $keyword;
                continue;
            }

            if ($keyword === 'every') {
                $weekDays = $this->parseList(
                    $walker->takeUntilSequence(array_map(
                        static fn ($keyword) => ' ' . $keyword . ' ',
                        self::KEYWORDS
                    ))
                );

                $event->when = $weekDays;
                continue;
            }

            if ($keyword === 'until') {
                $ends = $walker->takeUntil(' ');

                $event->startsAt = 'now';
                $event->endsAt   = $ends;
                continue;
            }

            if ($keyword === 'between') {
                $startsAt = $walker->takeUntilSequence([' and']);

                // We skip the " and"
                $walker->next(4);

                $endsAt = $walker->takeUntil(' ');

                $event->startsAt = $startsAt;
                $event->endsAt   = $endsAt;
                continue;
            }

            if ($keyword === 'for') {
                $measure = $walker->takeUntilSequence(array_map(
                    static fn ($unit) => ' ' . $unit,
                    array_keys(TimeUnit::NAMES)
                ));
                $unit = $walker->takeUntil(' ');

                $event->duration = (int) $measure * TimeUnit::convert($unit);
                continue;
            }

            if ($keyword === 'at') {
                $event->at = $walker->takeUntil(' ');
                continue;
            }

            if ($keyword === 'from') {
                $start = $walker->takeUntilSequence([' to']);

                // We skip the " to"
                $walker->next(3);
                $end = $walker->takeUntilSequence(array_map(
                    static fn ($keyword) => ' ' . $keyword . ' ',
                    self::KEYWORDS
                ));

                $event->at       = $start;
                $event->duration = $this->diffInSeconds(new DateTime($start), new DateTime($end));
            }

            if ($keyword === 'once') {
                $event->when = $walker->takeUntil(' ');
                continue;
            }

            $walker->next();
        }

        return $event;
    }

    protected function parseList(string $list): array
    {
        return array_map(
            'trim',
            // Flattens the array as it is at most two levels deep.
            array_merge(
                ...array_map(
                    static fn (string $item) => explode('and', $item),
                    explode(',', $list)
                )
            )
        );
    }

    protected function diffInSeconds(DateTimeInterface $start, DateTimeInterface $end): int
    {
        $diff = $end->diff($start);

        $daysInSecs    = ((int) $diff->format('%r%a')) * 24 * 60 * 60;
        $hoursInSecs   = $diff->h * 60 * 60;
        $minutesInSecs = $diff->i * 60;

        return $daysInSecs + $hoursInSecs + $minutesInSecs + $diff->s;
    }
}
