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
        protected Generator $generator,
        protected SemanticAnalyzer $semanticAnalyzer,
    ) {
    }

    public static function compile(string $code, ?CarbonPeriod $boundaries = null, ?CarbonInterface $now = null): array
    {
        return (new self(
            new Preprocessor(),
            new Lexer(),
            new Generator(),
            new SemanticAnalyzer()
        ))->process($code, $now ?? Carbon::now(), $boundaries);
    }

    public function process(string $raw, CarbonInterface $now, ?CarbonPeriod $boundaries): array
    {
        $code  = $this->preprocessor->preprocess($raw, $now);
        $event = $this->lexer->tokenize($code, $now);

        $this->semanticAnalyzer->analyze($event);

        return $this->generator->generate($event, $now, $boundaries);
    }
}
