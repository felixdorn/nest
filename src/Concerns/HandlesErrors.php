<?php

namespace Felix\Nest\Concerns;

use Felix\Nest\Exceptions\CompileErrorException;

trait HandlesErrors
{
    public function error_unless(bool $condition, string $message, mixed ...$bindings): void
    {
        $this->error_if(!$condition, $message, ...$bindings);
    }

    public function error_if(bool $condition, string $message, mixed ...$bindings): void
    {
        if ($condition) {
            $this->error($message, ...$bindings);
        }
    }

    public function error(string $message, mixed ...$bindings): void
    {
        throw new CompileErrorException(sprintf($message, ...$bindings));
    }
}
