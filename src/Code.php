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

    public function hasSymbol(int $n): bool
    {
        return array_key_exists($n, $this->symbols);
    }

    public function getSymbol(int $n): string
    {
        return $this->symbols[$n];
    }
}
