<?php

namespace Felix\StructuredTime\Exceptions;

use Exception;

class CompilerException extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
