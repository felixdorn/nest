<?php

namespace Felix\Nest;

class Walker
{
    public string $code;
    public int $length;
    public int $cursor;

    public function __construct(string $code)
    {
        $this->code   = $code;
        $this->length = strlen($code);
        $this->cursor = 0;
    }

    public function takeUntil(string $stop): string
    {
        $carry = '';

        while (!$this->eof()) {
            $previous = $this->lookBehind();
            $char     = $this->current();
            $this->next();

            if ($char === $stop) {
                if ($previous !== '\\') {
                    return $carry;
                }

                // We remove the escape symbol.
                $carry = substr($carry, 0, -1);
            }

            $carry .= $char;
        }

        return $carry;
    }

    public function eof(): bool
    {
        return $this->cursor >= $this->length;
    }

    public function lookBehind(int $n = 1): ?string
    {
        return $this->lookAhead(-$n);
    }

    public function lookAhead(int $n = 1): ?string
    {
        return $this->code[$this->cursor + $n] ?? null;
    }

    public function current(): string
    {
        return $this->code[$this->cursor];
    }

    public function next(int $n = 1): self
    {
        $this->cursor += $n;

        return $this;
    }

    public function takeUntilSequence(array $sequences): string
    {
        $carry = '';

        while (!$this->eof()) {
            $char = $this->current();
            $this->next();

            foreach ($sequences as $sequence) {
                if (str_ends_with($carry, $sequence)) {
                    $sequenceLength = strlen($sequence);
                    $this->rewind($sequenceLength);

                    return substr($carry, 0, -$sequenceLength);
                }
            }

            $carry .= $char;
        }

        return $carry;
    }

    public function rewind(int $n = 1): self
    {
        $this->cursor -= $n;

        return $this;
    }
}
