<?php

namespace Felix\Nest;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Felix\Nest\Exceptions\CompileErrorException;

class Nest
{
    public const VERSION = '0.1.0';

    private static ?self $instance = null;

    private Preprocessor $preprocessor;
    private Lexer $lexer;
    private SemanticAnalyzer $semanticAnalyzer;
    private Generator $generator;

    public function __construct()
    {
        $this->preprocessor     = new Preprocessor();
        $this->lexer            = new Lexer();
        $this->semanticAnalyzer = new SemanticAnalyzer();
        $this->generator        = new Generator();
    }

    public static function compile(string $code, CarbonPeriod $boundaries, ?CarbonInterface $now = null): array
    {
        [$output, $errors] = self::getInstance()->process($code, $boundaries, $now ?? Carbon::now());

        return count($errors) > 0 ? throw new CompileErrorException($errors[0]) : $output;
    }

    public function process(string $raw, CarbonPeriod $boundaries, CarbonInterface $now): array
    {
        $code  = $this->preprocessor->preprocess($raw, $now);
        $event = $this->lexer->tokenize($code, $now);

        $errors = $this->semanticAnalyzer->analyze($event);

        if (count($errors) > 0) {
            return [[], $errors];
        }

        return [$this->generator->generate($event, $boundaries), []];
    }

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public static function laxCompile(string $code, CarbonPeriod $boundaries, ?CarbonInterface $now = null): array
    {
        return self::getInstance()->process(
            $code,
            $boundaries,
            $now ?? Carbon::now()
        );
    }
}
