<?php

namespace Felix\StructuredTime;

use Carbon\Carbon;
use Felix\StructuredTime\Concerns\HandlesErrors;
use Felix\StructuredTime\Concerns\HandlesTypes;

class Tokenizer
{
    use HandlesErrors;
    use HandlesTypes;

    public function tokenize(string $code): array
    {
        $context = new Context($code);
        $token   = [
            'constraints' => [],
        ];

        while (!$context->eof()) {
            $char = $context->current();

            // Handles the label
            if ($char === '"') {
                // We skip the first quote
                $context->next();
                $token['label'] = $context->takeUntilChar('"');
                // We skip the closing quote
                $context->next();
                continue;
            }

            $keyword = $context->takeUntilChar(' ');

            if ($keyword === 'every') {
                // TODO: Support for "monday and sunday", "monday, sunday"
                $rawWeekDays = $context->takeUntilSequence([' every ', ' for ', ' between ', ' until ', ' at ']);
                $weekDays    = $this->explodeNaturalList($rawWeekDays);

                foreach ($weekDays as $weekDay) {
                    $this->errorUnless($this->isWeekday($weekDay), 'every must be followed by a weekday, "%s" given', $weekDay);
                }

                // TODO: Support for "monday and sunday", "monday, sunday"
                $token['when'] = $weekDays;
                continue;
            }

            if ($keyword === 'between') {
                $startsAt = $context->takeUntilSequence(' and');

                $this->errorUnless($this->isDate($startsAt), 'a valid date is expected after between, given %s', $startsAt);

                // We skip the " and"
                $context->next(4);

                $endsAt = $context->takeUntilChar(' ');
                $this->errorUnless($this->isDate($endsAt), 'a valid date is expected after and, given %s', $endsAt);

                $token['constraints']['between'] = [$startsAt, $endsAt];
                continue;
            }

            if ($keyword === 'until') {
                $endsAt = $context->takeUntilSequence(' ');
                $this->errorUnless($this->isDate($endsAt), 'a valid date is expected after until, given %s', $endsAt);

                $token['constraints']['between'] = [Carbon::now()->format('d/m/Y'), $endsAt];
                continue;
            }

            if ($keyword === 'at') {
                $time = $context->takeUntilChar(' ');

                $this->errorUnless($this->isTime($time), 'a valid time is expected after at, given: %s', $time);

                $token['constraints']['at'] = $time;
                continue;
            }

            if ($keyword === 'for') {
                $measure = $context->takeUntilChar(' ');

                $this->errorUnless($this->isNumber($measure), 'for must be followed by a number, given: %s', $measure);

                $unit = $context->takeUntilChar(' ');

                $this->errorUnless($this->isTimeUnit($unit), 'unexpected time unit, given: %s', $unit);

                $token['constraints']['duration'] = (float) $measure * TimeUnit::convert($unit);
                continue;
            }

            $this->error('unexpected token "%s"', $keyword);
        }

        dd($token);

        return $token;
    }

    private function explodeNaturalList(string $rawWeekDays): array
    {
        $weekDays = explode(',', $rawWeekDays);

        foreach ($weekDays as $k => $item) {
            $weekDays[$k] = explode('and', $item);
        }

        // Flattens the array as it's only two levels deep.
        return array_map('trim', array_merge(...$weekDays));
    }
}
