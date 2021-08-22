<?php

namespace Felix\StructuredTime\Compiler;

class Compiler
{
    protected Preprocessor $preprocessor;
    protected Tokenizer $tokenizer;

    public function __construct()
    {
        $this->preprocessor = new Preprocessor();
        $this->tokenizer    = new Tokenizer();
    }

    public function compile(string $code): array
    {
        $context = $this->preprocessor->process(
            new Context($code)
        );

        dd((string) $context);

        return $this->tokenizer->tokenize($context);
    }
}
