<?php

namespace Felix\Nest;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class Generator
{
    public function generate(Event $event, CarbonInterface $current, CarbonPeriod $period): array
    {
        $occurrences = [];

        foreach ($period as $day) {
            if (is_array($event->when)) {
                foreach ($event->when as $weekDay) {
                    if (strtolower($day->dayName) !== $weekDay) {
                        continue;
                    }

                    $start         = $this->setTimeFromEvent($day->clone(), $event);
                    $occurrences[] = [$start->toDateTimeString(), $start->clone()->addSeconds($event->duration)->toDateTimeString()];
                }
            } elseif (is_string($event->when)) {
                if ($day->toDateString() === $event->when) {
                    $start = $this->setTimeFromEvent($day->clone(), $event);
                    $occurrences[] = [$start->toDateTimeString(), $start->clone()->addSeconds($event->duration)->toDateTimeString()];
                }
            }
        }

        return [
            'label'       => $event->label,
            'now'         => $current->toDateTimeString(),
            'occurrences' => $occurrences,
        ];
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
