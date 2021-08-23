<?php

use Carbon\Carbon;
use Felix\Nest\Lexer;
use Felix\Nest\Preprocessor;
use Felix\Nest\Support\TimeUnit;

dataset('compilations', [
    ['every monday from 12:15AM to 4PM', [
        'when'     => ['monday'],
        'at'       => '00:15',
        'duration' => TimeUnit::HOUR * 15 + TimeUnit::MINUTE * 45,
    ]],
    ['everyday for an hour at 6AM', [
        'when'     => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
        'duration' => TimeUnit::HOUR,
        'at'       => '06:00',
    ]],
    ['every wednesday at 15:05 for 55 minutes', [
        'when'     => ['wednesday'],
        'at'       => '15:05',
        'duration' => TimeUnit::HOUR - (TimeUnit::MINUTE * 5),
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
