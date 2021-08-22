<?php

namespace Felix\Nest\Compiler;

use Felix\Nest\Concerns\HandlesTokenization;

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

    public function __toString(): string
    {
        return $this->code;
    }

    public function toString(): string
    {
        return (string) $this;
    }
}
