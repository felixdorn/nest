<?php

namespace Felix\Nest;

use Carbon\CarbonInterface;

class Compiler
{
    protected Preprocessor $preprocessor;
    protected Lexer $lexer;
    protected SyntaxAnalyzer $syntaxAnalyzer;
    protected SemanticAnalyzer $semanticAnalyzer;
    protected IntermediateCodeGenerator $intermediateCodeGenerator;
    protected Generator $generator;

    public function __construct()
    {
        $this->preprocessor     = new Preprocessor();
        $this->lexer            = new Lexer();
        $this->semanticAnalyzer = new SemanticAnalyzer();
        $this->generator        = new Generator();
    }

    public function compile(string $raw, CarbonInterface $current): array
    {
        $code  = $this->preprocessor->preprocess($raw, $current);
        $event = $this->lexer->tokenize($code);

        $this->semanticAnalyzer->validate($event);

        return $this->generator->generate($event);
    }
}
