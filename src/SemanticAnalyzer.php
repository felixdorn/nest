<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class SemanticAnalyzer
{
    public function analyze(Event $event): array
    {
        $errors = [];

        foreach ($event->when as $when) {
            $isValidWeekday = in_array($when, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);

            if ($isValidWeekday) {
                continue;
            }

            try {
                Carbon::parse($when);
            } catch (InvalidFormatException) {
                $errors[] = sprintf('Invalid date: %s', $when);
            }
        }

        try {
            Carbon::parse($event->startsAt);
        } catch (InvalidFormatException) {
            $errors[] = sprintf('Invalid date: %s', $event->startsAt);
        }

        try {
            Carbon::parse($event->endsAt);
        } catch (InvalidFormatException) {
            $errors[] = sprintf('Invalid date: %s', $event->endsAt);
        }

        return $errors;
    }
}
