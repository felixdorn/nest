<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Felix\Nest\Exceptions\CompileErrorException;

class Event
{
    private CarbonInterface|null $startsAt = null;
    private CarbonInterface|null $endsAt   = null;
    private array $when                    = [];
    private string|null $at                = null;
    private int $duration                  = 0;

    public function at(): ?string
    {
        return $this->at;
    }

    public function setAt(string $time): self
    {
        $this->at = $time;

        return $this;
    }

    public function duration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function startsAt(): ?CarbonInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(string|CarbonInterface $value): self
    {
        if (is_string($value)) {
            try {
                $value = Carbon::parse($value);
            } catch (InvalidFormatException) {
                throw new CompileErrorException('Invalid date: ' . $value);
            }
        }

        $this->startsAt = $value;

        return $this;
    }

    public function endsAt(): ?CarbonInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt($value): self
    {
        if (is_string($value)) {
            try {
                $value = Carbon::parse($value);
            } catch (InvalidFormatException) {
                throw new CompileErrorException('Invalid date: ' . $value);
            }
        }

        $this->endsAt = $value;

        return $this;
    }

    public function when(): array
    {
        return $this->when;
    }

    public function setWhen(array $when): self
    {
        $this->when = [];

        foreach ($when as $item) {
            $this->addWhen($item);
        }

        return $this;
    }

    public function addWhen(string $when): self
    {
        if (!in_array($when, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])) {
            try {
                $when = Carbon::parse($when);
            } catch (InvalidFormatException) {
                throw new CompileErrorException('Invalid date: ' . $when);
            }
        }

        if ($when instanceof CarbonInterface) {
            $when = $when->toDateString();
        }

        $this->when[] = $when;

        return $this;
    }
}
