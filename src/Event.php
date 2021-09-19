<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Felix\Nest\Exceptions\CompileErrorException;

class Event
{
    private string|null $label             = null;
    private array $occurrences             = [];
    private array $compilationErrors       = [];
    private CarbonInterface|null $startsAt = null;
    private CarbonInterface|null $endsAt   = null;
    private array $when                    = [];
    private string|null $at                = null;
    private int $duration                  = 0;

    public function addOccurrence(CarbonInterface $startsAt, CarbonInterface $endsAt): self
    {
        $this->occurrences[] = ['starts_at' => $startsAt->toDateTimeString('minute'), 'ends_at' => $endsAt->toDateTimeString('minute')];

        return $this;
    }

    public function addCompilationError(string $message, string ...$bindings): void
    {
        throw new CompileErrorException(sprintf($message, ...$bindings));
    }

    public function getAt(): ?string
    {
        return $this->at;
    }

    public function setAt(string $time): self
    {
        $this->at = $time;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCompilationErrors(): array
    {
        return $this->compilationErrors;
    }

    public function getOccurrences(): array
    {
        return $this->occurrences;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getStartsAt(): ?CarbonInterface
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

    public function getEndsAt(): ?CarbonInterface
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

    public function getWhen(): array
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
