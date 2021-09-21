<?php

namespace Felix\Nest;

use Carbon\CarbonInterface;
use DateTime;
use DateTimeInterface;
use Felix\Nest\Exceptions\CompileErrorException;
use Felix\Nest\Support\Arr;
use Felix\Nest\Support\TimeUnit;

class Lexer
{
    public const KEYWORDS = ['every', 'for', 'between', 'until', 'at', 'from', 'once', 'in'];

    public function tokenize(string $code, CarbonInterface $now): Event
    {
        $walker = new Walker($code);
        $event  = new Event();

        while (!$walker->eof()) {
            if ($walker->current() === '"') {
                // Skipping the starting quote
                $walker->next();
                $event->setLabel($walker->takeUntil('"'));
                // Skipping the closing quote
                $walker->next();
                continue;
            }

            $keyword = $walker->takeUntil(' ');

            // Implicit once keyword
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $keyword)) {
                $event->addWhen($keyword);
                continue;
            }

            if ($keyword === 'every') {
                $weekDays = $this->parseList(
                    $walker->takeUntilSequence(array_map(
                        static fn ($keyword) => ' ' . $keyword . ' ',
                        self::KEYWORDS
                    ))
                );

                $event->setWhen(array_map('strtolower', $weekDays));
                continue;
            }

            if ($keyword === 'until') {
                $ends = $walker->takeUntil(' ');

                $event->setStartsAt($now);
                $event->setEndsAt($ends);
                continue;
            }

            if ($keyword === 'between') {
                $startsAt = $walker->takeUntilSequence([' and']);

                // We skip the " and"
                $walker->next(4);

                $endsAt = $walker->takeUntil(' ');

                $event->setStartsAt($startsAt);
                $event->setEndsAt($endsAt);
                continue;
            }

            if ($keyword === 'for') {
                $measure = $walker->takeUntilSequence(array_map(
                    static fn ($unit) => ' ' . $unit,
                    array_keys(TimeUnit::NAMES)
                ));
                $unit = $walker->takeUntil(' ');

                $event->setDuration((int) $measure * TimeUnit::convert($unit));
                continue;
            }

            if ($keyword === 'at') {
                $event->setAt($walker->takeUntil(' '));
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

                $event->setAt($start);
                $event->setDuration($this->diffInSeconds(new DateTime($start), new DateTime($end)));
                continue;
            }

            if ($keyword === 'once') {
                $event->addWhen($walker->takeUntil(' '));
                continue;
            }

            if ($keyword === 'in') {
                $measure = $walker->takeUntilSequence(array_map(
                    static fn ($unit) => ' ' . $unit,
                    array_keys(TimeUnit::NAMES)
                ));
                $unit     = TimeUnit::convert($walker->takeUntil(' '));
                $duration = (int) $measure * $unit;
                $event->addWhen($now->clone()->addSeconds($duration)->toDateString());
                if ($unit <= TimeUnit::HOUR) {
                    $event->setAt($now->clone()->addSeconds($duration)->toTimeString('minute'));
                }

                continue;
            }

            throw new CompileErrorException('Syntax error, unexpected ' . $keyword);
        }

        $event->setWhen(Arr::flatten($event->when()));

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
