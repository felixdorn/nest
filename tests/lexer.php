<?php

use Carbon\Carbon;
use Felix\Nest\Lexer;
use Felix\Nest\Preprocessor;
use Felix\Nest\Support\TimeUnit;

dataset('compilations', [
    ['every monday from 12:15AM to 4PM', [
        'when'     => ['monday'],
        'at'       => '00:15',
        'duration' => 15 * TimeUnit::HOUR + 45 * TimeUnit::MINUTE,
    ]],
    ['everyday for an hour at 6AM', [
        'when'     => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
        'duration' => TimeUnit::HOUR,
        'at'       => '06:00',
    ]],
    ['every wednesday at 15:05 for 55 minutes', [
        'when'     => ['wednesday'],
        'at'       => '15:05',
        'duration' => 55 * TimeUnit::MINUTE,
    ]],
    ['every monday, wednesday, friday and sunday at 22 for an hour between 15/04/2005 and 16/05/2006', [
        'when'      => ['monday', 'wednesday', 'friday', 'sunday'],
        'at'        => '22:00',
        'duration'  => TimeUnit::HOUR,
        'starts_at' => '15/04/2005',
        'ends_at'   => '16/05/2006',
    ]],
    ['for two minutes', [
        'duration' => 2 * TimeUnit::MINUTE,
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
