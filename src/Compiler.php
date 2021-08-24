<?php

namespace Felix\Nest;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class Compiler
{
    public function __construct(
        protected Preprocessor $preprocessor,
        protected Lexer $lexer,
        protected Generator $generator
    ) {
    }

    public function compile(string $raw, CarbonInterface $now, CarbonPeriod $boundaries): array
    {
        $code  = $this->preprocessor->preprocess($raw, $now);
        $event = $this->lexer->tokenize($code, $now);

        return $this->generator->generate($event, $now, $boundaries);
    }
}
