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
    ['1/1/21', '2021-01-01'],
    ['01/1/45', '2045-01-01'],
    ['2/2/1', '2001-02-02'],
    ['15/04/2005', '2005-04-15'],
    ['16/5/2006', '2006-05-16'],
    ['5/11/06', '2006-11-05'],
    ['2006-5-1', '2006-05-01'],
    ['6-5-22', '2006-05-22'],
    ['04-12-1', '2004-12-01'],
    ['everyday', 'every monday, tuesday, wednesday, thursday, friday, saturday, sunday'],
    ['a', '1'],
    ['an', '1'],
    ['"some Label like that"', '"some Label like that"'],
    ['weekend', 'saturday and sunday'],
    ['week-end', 'saturday and sunday'],
    ['"hello \"world\""', '"hello \"world\""'],
]);

beforeEach(function () {
    $this->preprocessor = new Preprocessor();
});

it('processes', function (string $code, string $result) {
    expect($this->preprocessor->preprocess($code, Carbon::now()))->toBe($result);
})->with('processed');
