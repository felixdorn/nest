<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Felix\Nest\Support\TimeUnit;

class Lexer
{
    public function tokenize(Code $code): array
    {
        $walker = new Walker($code);
        $event = ['symbols' => $code->getSymbols()];

        while (!$walker->eof()) {
            $keyword = $walker->takeUntil(' ');

            if ($keyword === 'every') {
                $weekDays = $this->parseList(
                    $walker->takeUntilSequence([' every ', ' for ', ' between ', ' until ', ' at ', ' from ']) // TODO: Should not be hard coded
                );

                $event['when'] = $weekDays;
                continue;
            }

            if ($keyword === 'until') {
                $ends = $walker->takeUntil(' ');

                $token['ends_at'] = $ends;
                continue;
            }

            if ($keyword === 'between') {
                $startsAt = $walker->takeUntilSequence([' and']);

                // We skip the " and"
                $walker->next(4);

                $endsAt = $walker->takeUntil(' ');

                $event['starts_at'] = $startsAt;
                $event['ends_at'] = $endsAt;
                continue;
            }

            if ($keyword === 'for') {
                // TODO: Plural and shorthands (like: 1min, 2h) + should not be hardcoded
                $measure = $walker->takeUntilSequence([' minute', ' hour', ' day', ' week']);
                $unit = $walker->takeUntil(' ');

                $event['duration'] = (float)$measure * TimeUnit::convert($unit);
                continue;
            }

            if ($keyword === 'at') {
                $time = $walker->takeUntil(' ');

                $event['at'] = $time;
                continue;
            }

            if ($keyword === 'from') {
                $startSymbol = $walker->takeUntilSequence([' to']);
                $start = $code->getSymbol($startSymbol);

                // We skip the " to"
                $walker->next(3);
                $endSymbol = $walker->takeUntilSequence([' every ', ' for ', ' between ', ' until ', ' at ', ' from ']); // TODO: Should not be hard coded
                $end = $code->getSymbol($endSymbol);

                $period = CarbonPeriod::create(
                    Carbon::parse('2000-01-01 ' . $start),
                    Carbon::parse('2000-01-01 ' . $end)
                );
                dd($period);
                dd($start, $end);
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
                    static fn(string $item) => explode('and', $item),
                    explode(',', $list)
                )
            )
        );
    }
}
