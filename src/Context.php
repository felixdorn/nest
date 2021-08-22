<?php

namespace Felix\StructuredTime;

use Felix\StructuredTime\Concerns\HandlesTokenization;

class Context
{
    use HandlesTokenization;

    private int $length;
    private int $cursor;
    private bool $debug;
    private ?string $current;

    public function __construct(private string $code)
    {
        $this->length = strlen($code);
        $this->cursor = 0;
    }

    public function current(): string
    {
        return $this->code[$this->cursor];
    }

    public function eof(): bool
    {
        return $this->length <= $this->cursor;
    }

    public function rewind(int $n = -1): void
    {
        $this->cursor -= $n;
    }

    public function next(int $n = 1): void
    {
        $this->cursor += $n;
    }

    public function lookBehind(int $n = 1): ?string
    {
        return $this->lookAhead(-$n);
    }

    public function lookAhead(int $n = 1): ?string
    {
        return $this->code[$this->cursor + $n] ?? null;
    }
}
