<?php

use Carbon\Carbon;
use Felix\Nest\Lexer;
use Felix\Nest\Preprocessor;

dataset('compilations', [
    ['every monday from 12:15AM to 4PM', [
        'when'     => ['monday'],
        'at'       => '00:15',
        'duration' => 56700,
    ]],
    ['everyday for an hour at 6AM', [
        'when'     => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
        'duration' => 3600,
        'at'       => '06:00',
    ]],
]);

beforeEach(function () {
    $this->preprocessor = new Preprocessor();
    $this->lexer = new Lexer();
});

it('compiles', function (string $code, array $expectedEvent) {
    $event = $this->lexer->tokenize(
        $this->preprocessor->preprocess($code, Carbon::now())
    );

    expect($event)->toBe($expectedEvent);
})->with('compilations');
