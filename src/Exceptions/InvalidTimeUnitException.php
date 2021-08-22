<?php

namespace Felix\StructuredTime\Exceptions;

use Exception;

class InvalidTimeUnitException extends Exception
{
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
