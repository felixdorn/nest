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

            foreach ($event->when as $when) {
                [$kind, $value] = $when;

                if ($kind === 'every') {
                    $this->error_if(
                        !in_array($value, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                        'Syntax error, unexpected weekday %s',
                        $value
                    );
                }

                if ($kind === 'once') {
                    Carbon::parse($value);
                }
            }
        } catch (InvalidFormatException) {
            $this->error('Invalid format for a date, must be either d/m/y or y-m-d');
        }
    }
}
