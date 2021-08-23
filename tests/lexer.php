<?php

use Carbon\Carbon;
use Felix\Nest\Lexer;
use Felix\Nest\Preprocessor;

it('works', function () {
    $preprocessor = new Preprocessor();
    $lexer = new Lexer();

    $token = $lexer->tokenize(
        $preprocessor->preprocess('every monday from 12:15AM to 17PM', Carbon::now())
//        $preprocessor->preprocess('everyday for an hour at 6AM', Carbon::now())
    );

    dd($token);
});
