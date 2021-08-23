<?php

use Carbon\Carbon;
use Felix\Nest\Preprocessor;

dataset('processed', [
    ['6', '06:00'],
    ['18', '18:00'],
    ['1:0', '01:00'],
    ['1:4', '01:04'],
    ['12:45', '12:45'],
    ['00:00', '00:00'],
    ['0', '00:00'],
    ['1/1/21', '01/01/2021'],
    ['01/1/45', '01/01/2045'],
    ['2/2/1', '02/02/2001'],
    ['04/15/2005', '04/15/2005'],
    ['5/16/2006', '05/16/2006'],
    ['5/16/06', '05/16/2006'],
]);

beforeEach(function () {
    $this->preprocessor = new Preprocessor();
});

it('converts dates and times to symbols', function (string $current, string $processed) {
    $code = $this->preprocessor->preprocess($current, Carbon::now());

    expect($code->getSymbol(0))->toBe($processed);
})->with('processed')->only();

it('converts everyday to a list of days', function () {
    $code = $this->preprocessor->preprocess('everyday', Carbon::now());

    expect((string) $code)->toBe('every monday, tuesday, wednesday, thursday, friday, saturday, sunday');
});

it('converts a to 1', function () {
    $code = $this->preprocessor->preprocess('a', Carbon::now());

    expect((string) $code)->toBe('1');
});

it('converts an to 1', function () {
    $code = $this->preprocessor->preprocess('every monday for an hour', Carbon::now());

    expect((string) $code)->toBe('every monday for 1 hour');
});
