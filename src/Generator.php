<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Felix\Nest\Concerns\HandlesErrors;

class Generator
{
    use HandlesErrors;

    public function generate(Event $event, CarbonInterface $current, ?CarbonPeriod $period): array
    {
        $occurrences = [];

        $periodWithinBoundaries = $this->findRealEventBoundaries($event, $period);

        /** @var CarbonInterface $day */
        foreach ($periodWithinBoundaries as $day) {
            foreach ($event->when as $when) {
                [$kind, $value] = $when;

                if ($kind === 'once' && !$day->isSameDay(Carbon::parse($value))) {
                    continue;
                }

                if ($kind === 'every' && $value !== $day->dayName) {
                    continue;
                }

                $start         = $this->setTimeFromEvent($day->clone(), $event);
                $occurrences[] = [
                    'starts_at' => $start->toDateTimeString(),
                    'ends_at'   => $start->clone()->addSeconds($event->duration)->toDateTimeString(),
                ];
            }
        }

        return [
            'label'       => $event->label,
            'now'         => $current->toDateTimeString(),
            'occurrences' => $occurrences,
        ];
    }

    /**
     * This function find the smallest time window that the event occurs in.
     * If the code below looks unclear to you, here's a cleaner version:.
     *
     * CarbonPeriod::create(min($event->startsAt, $period->start), min($event->endsAt, $period->end)).
     */
    private function findRealEventBoundaries(Event $event, ?CarbonPeriod $period): CarbonPeriod
    {
        if ($period === null) {
            $this->error_if(
                $event->startsAt === null && $event->endsAt === null,
                'No boundaries set for an infinitely repeatable event.'
            );

            return CarbonPeriod::create(
                Carbon::parse($event->startsAt),
                Carbon::parse($event->endsAt)
            );
        }

        if ($event->startsAt === null) {
            $start = $period->start;
        } else {
            $eventStartsAt = Carbon::parse($event->startsAt);

            if ($eventStartsAt->lessThan($period->start)) {
                $start = $eventStartsAt;
            } else {
                $start = $period->start;
            }
        }

        if ($event->endsAt === null) {
            $end = $period->end;
        } else {
            $eventEndsAt = Carbon::parse($event->endsAt);

            if ($eventEndsAt->lessThan($period->end)) {
                $end = $eventEndsAt;
            } else {
                $end = $period->end;
            }
        }

        return CarbonPeriod::create($start, $end);
    }

    private function setTimeFromEvent(CarbonInterface $date, Event $event): CarbonInterface
    {
        if (is_string($event->at)) {
            [$hours, $minutes] = explode(':', $event->at);

            $date->hours((int) $hours)->minutes((int) $minutes);
        }

        return $date;
    }
}
