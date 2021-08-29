<?php

namespace Felix\Nest;

class Event
{
    public function __construct(
        public array $when = [],
        public ?string $label = null,
        public ?string $startsAt = null,
        public ?string $endsAt = null,
        public ?string $at = null,
        public int $duration = 0
    ) {
    }

    public function toArray(): array
    {
        return [
            'when'     => $this->when,
            'label'    => $this->label,
            'startsAt' => $this->startsAt,
            'endsAt'   => $this->endsAt,
            'at'       => $this->at,
            'duration' => $this->duration,
        ];
    }
}
