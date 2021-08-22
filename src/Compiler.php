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
        $this->preprocessor              = new Preprocessor();
        $this->lexer                     = new Lexer();
        $this->syntaxAnalyzer            = new SyntaxAnalyzer();
        $this->semanticAnalyzer          = new SemanticAnalyzer();
        $this->intermediateCodeGenerator = new IntermediateCodeGenerator();
        $this->generator                 = new Generator();
    }

    public function compile(string $raw, CarbonInterface $current): array
    {
        $code       = $this->preprocessor->preprocess($raw, $current);
        $tokens     = $this->lexer->tokenize($code);
        $syntaxTree = $this->syntaxAnalyzer->tree($tokens);

        $this->semanticAnalyzer->validate($syntaxTree);

        return $this->generator->generate(
            $this->intermediateCodeGenerator->generate($syntaxTree)
        );
    }
}
