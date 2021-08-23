<?php

namespace Felix\Nest;

class Code
{
    protected string $code;
    protected array $symbols = [];

    public function __construct(string $code, array $symbols = [])
    {
        $this->code    = $code;
        $this->symbols = $symbols;
    }

    /**
     * @return int Returns the symbol label
     */
    public function addSymbol(string $symbol): int
    {
        $this->symbols[] = $symbol;

        return array_key_last($this->symbols);
    }

    public function hasSymbol(int $n): bool
    {
        return array_key_exists($n, $this->symbols);
    }

    public function getSymbol(int|string $n): string
    {
        if (is_string($n)) {
            $n = (int) str_replace('$', '', $n);
        }

        return $this->symbols[$n];
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->code;
    }

    public function getSymbols(): array
    {
        return $this->symbols;
    }
}
