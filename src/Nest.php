<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Felix\Nest\Exceptions\CompileErrorException;

class Nest
{
    public const VERSION = '0.1.0';

    private static ?Preprocessor $preprocessor         = null;
    private static ?Lexer $lexer                       = null;
    private static ?SemanticAnalyzer $semanticAnalyzer = null;
    private static ?Generator $generator               = null;

    public static function compile(string $code, CarbonPeriod $boundaries, ?CarbonInterface $now = null): array
    {
        [$output, $errors] = (new self())->process($code, $boundaries, $now ?? Carbon::now());

        return count($errors) > 0 ? throw new CompileErrorException($errors[0]) : $output;
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

        $errors = self::$semanticAnalyzer->analyze($event);

        if (count($errors) > 0) {
            return [[], $errors];
        }

        return [self::$generator->generate($event, $boundaries), []];
    }

    public static function laxCompile(string $code, CarbonPeriod $boundaries, ?CarbonInterface $now = null): array
    {
        [$output, $errors] = (new self())->process($code, $boundaries, $now ?? Carbon::now());

        return [$output, $errors];
    }
}
