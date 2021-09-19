<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class Generator
{
    public function generate(Event $event, CarbonPeriod $boundaries): Event
    {
        $realBoundaries = $this->findBoundaries(
            $event->getStartsAt() !== null ? Carbon::parse($event->getStartsAt()) : null,
            $event->getEndsAt() !== null ? Carbon::parse($event->getEndsAt()) : null,
            $boundaries
        );

        foreach ($realBoundaries as $day) {
            if ($day === null) {
                continue;
            }

            if (!in_array(strtolower($day->dayName), $event->getWhen()) &&
                !in_array($day->toDateString(), $event->getWhen())) {
                continue;
            }

            $start = $day;

            if (!is_null($event->getAt())) {
                [$hours, $minutes] = explode(':', $event->getAt());

                $start = $start->hours((int) $hours)->minutes((int) $minutes);
            }

            $event->addOccurrence($start, $start->clone()->addSeconds($event->getDuration()));
        }

        return $event;
    }

    protected function findBoundaries(?CarbonInterface $start, ?CarbonInterface $end, CarbonPeriod $boundaries): CarbonPeriod
    {
        $start ??= $boundaries->start;
        $end ??= $boundaries->end;

        return CarbonPeriod::create(
            $boundaries->start->greaterThan($start) ? $boundaries->start : $start,
            $boundaries->end->lessThan($end) ? $boundaries->end : $end
        );
    }
}
