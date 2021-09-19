<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class Generator
{
    public function generate(Event $event, CarbonPeriod $boundaries): array
    {
        $occurrences = [];

        $realBoundaries = $this->findBoundaries(
            $event->startsAt !== null ? Carbon::parse($event->startsAt) : null,
            $event->endsAt !== null ? Carbon::parse($event->endsAt) : null,
            $boundaries
        );

        foreach ($realBoundaries as $day) {
            if ($day === null) {
                continue;
            }

            if (!in_array(strtolower($day->dayName), $event->when) &&
                !in_array($day->toDateString(), $event->when)) {
                continue;
            }

            $start = $day;

            if (!is_null($event->at)) {
                [$hours, $minutes] = explode(':', $event->at);

                $start = $start->hours((int) $hours)->minutes((int) $minutes);
            }

            $occurrences[] = [
                'label'     => $event->label,
                'starts_at' => $start->toDateTimeString(),
                'ends_at'   => $start->clone()->addSeconds($event->duration)->toDateTimeString(),
            ];
        }

        return $occurrences;
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
