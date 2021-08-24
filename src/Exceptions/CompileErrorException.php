<?php

namespace Felix\Nest\Exceptions;

use Exception;

class CompileErrorException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
