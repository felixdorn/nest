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
        try {
            Carbon::parse($event->startsAt);
            Carbon::parse($event->endsAt);

            if (is_string($event->when)) {
                Carbon::parse($event->when);
            }

            if (is_array($event->when)) {
                foreach ($event->when as $weekDay) {
                    $this->error_if(
                        !in_array($weekDay, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                        'Syntax error, unexpected weekday %s',
                        $weekDay
                    );
                }
            }
        } catch (InvalidFormatException) {
            $this->error('Invalid format for a date, must be either d/m/y or y-m-d');
        }
    }
}
