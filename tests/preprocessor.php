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
    ['12AM', '00:00'],
    ['12PM', '12:00'],
    ['4:12AM', '04:12'],
    ['4:12PM', '16:12'],
    ['1PM', '13:00'],
    ['12:15am', '00:15'],
    ['1/1/21', '01/01/2021'],
    ['01/1/45', '01/01/2045'],
    ['2/2/1', '02/02/2001'],
    ['04/15/2005', '04/15/2005'],
    ['5/16/2006', '05/16/2006'],
    ['5/16/06', '05/16/2006'],
    ['everyday', 'every monday, tuesday, wednesday, thursday, friday, saturday, sunday'],
    ['a', '1'],
    ['an', '1'],
    ['"some Label like that"', '"some Label like that"'],
]);

beforeEach(function () {
    $this->preprocessor = new Preprocessor();
});

it('processes', function (string $code, string $result) {
    expect($this->preprocessor->preprocess($code, Carbon::now()))->toBe($result);
})->with('processed');
