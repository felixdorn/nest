<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Felix\Nest\Concerns\HandlesErrors;

class SemanticAnalyzer
{
    use HandlesErrors;

    public function analyze(Event $event): void
    {
        foreach ($event->when as $when) {
            $isValidWeekday = in_array($when, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);

            if ($isValidWeekday) {
                continue;
            }

            try {
                Carbon::parse($when);
            } catch (InvalidFormatException) {
                $this->error('Invalid date: %s', $when);
                continue;
            }

            $this->error('Invalid weekday: %s', $when);
        }

        try {
            Carbon::parse($event->startsAt);
        } catch (InvalidFormatException) {
            $this->error('Invalid date: %s', $event->startsAt);
        }

        try {
            Carbon::parse($event->endsAt);
        } catch (InvalidFormatException) {
            $this->error('Invalid date: %s', $event->endsAt);
        }


    }
}
