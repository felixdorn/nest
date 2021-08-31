<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class Nest
{
    public const VERSION = '0.1.0';

    private static ?Preprocessor $preprocessor         = null;
    private static ?Lexer $lexer                       = null;
    private static ?SemanticAnalyzer $semanticAnalyzer = null;
    private static ?Generator $generator               = null;

    public static function compile(string $code, CarbonPeriod $boundaries, ?CarbonInterface $now = null): array
    {
        return (new self())->process($code, $boundaries, $now ?? Carbon::now());
    }

    public function process(string $raw, CarbonPeriod $boundaries, CarbonInterface $now): array
    {
        if (self::$preprocessor === null) {
            self::$preprocessor = new Preprocessor();
        }

        if (self::$lexer === null) {
            self::$lexer = new Lexer();
        }

        if (self::$semanticAnalyzer === null) {
            self::$semanticAnalyzer = new SemanticAnalyzer();
        }

        if (self::$generator === null) {
            self::$generator = new Generator();
        }

        $code  = self::$preprocessor->preprocess($raw, $now);
        $event = self::$lexer->tokenize($code, $now);

        self::$semanticAnalyzer->analyze($event);

        return self::$generator->generate($event, $boundaries);
    }
}
