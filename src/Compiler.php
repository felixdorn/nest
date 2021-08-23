<?php

namespace Felix\Nest;

use Carbon\CarbonInterface;

class Compiler
{
    public function __construct(
        protected Preprocessor $preprocessor,
        protected Lexer $lexer,
        protected Generator $generator
    ) {
    }

    public function compile(string $raw, CarbonInterface $current): array
    {
        $code  = $this->preprocessor->preprocess($raw, $current);
        $event = $this->lexer->tokenize($code);

        return $this->generator->generate($event, $current);
    }
}
