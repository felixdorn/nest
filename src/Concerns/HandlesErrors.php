<?php

namespace Felix\Nest\Concerns;

use Felix\Nest\Exceptions\CompilerException;

trait HandlesErrors
{
    public function errorIf(bool $condition, string $message, ...$bindings): void
    {
        if ($condition) {
            $this->error($message, ...$bindings);
        }
    }

    public function error(string $message, ...$bindings): void
    {
        throw new CompilerException('compile error: ' . sprintf($message, ...$bindings));
    }

    public function errorUnless(bool $condition, string $message, ...$bindings): void
    {
        if (!$condition) {
            $this->error($message, ...$bindings);
        }
    }
}
