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
    private Generator $generator;

    public function __construct()
    {
        $this->preprocessor     = new Preprocessor();
        $this->lexer            = new Lexer();
        $this->generator        = new Generator();
    }

    public static function compile(string $code, CarbonPeriod $boundaries, ?CarbonInterface $now = null): Event
    {
        $event = self::getInstance()->process($code, $boundaries, $now ?? Carbon::now());

        return !empty($event->getCompilationErrors()) ? throw new CompileErrorException($event->getCompilationErrors()[0]) : $event;
    }

    public function process(string $raw, CarbonPeriod $boundaries, CarbonInterface $now): Event
    {
        $code  = $this->preprocessor->preprocess($raw, $now);
        $event = $this->lexer->tokenize($code, $now);

        return $this->generator->generate($event, $boundaries);
    }

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public static function laxCompile(string $code, CarbonPeriod $boundaries, ?CarbonInterface $now = null): Event
    {
        return self::getInstance()->process(
            $code,
            $boundaries,
            $now ?? Carbon::now()
        );
    }
}
