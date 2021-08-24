<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class Nest
{
    public function __construct(
        protected Preprocessor $preprocessor,
        protected Lexer $lexer,
        protected Generator $generator
    ) {
    }

    public static function compile(string $code, ?CarbonPeriod $boundaries = null, ?CarbonInterface $now = null): array
    {
        return (new static(
            new Preprocessor(),
            new Lexer(),
            new Generator()
        ))->process($code, $now ?? Carbon::now(), $boundaries);
    }

    public function process(string $raw, CarbonInterface $now, ?CarbonPeriod $boundaries): array
    {
        $code  = $this->preprocessor->preprocess($raw, $now);
        $event = $this->lexer->tokenize($code, $now);

        return $this->generator->generate($event, $now, $boundaries);
    }
}
