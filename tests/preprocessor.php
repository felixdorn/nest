<?php

use Carbon\Carbon;
use Felix\Nest\Preprocessor;

dataset('processed', [
    ['at 6', 'at 06:00'],
    ['at 18', 'at 18:00'],
    ['at 1:0', 'at 01:00'],
    ['at 1:4', 'at 01:04'],
    ['at 12:45', 'at 12:45'],
    ['at 00:00', 'at 00:00'],
    ['at 0', 'at 00:00'],
    ['at 12AM', 'at 00:00'],
    ['at 12PM', 'at 12:00'],
    ['at 4:12AM', 'at 04:12'],
    ['at 4:12PM', 'at 16:12'],
    ['at 1PM', 'at 13:00'],
    ['at 12:15am', 'at 00:15'],
    ['until 1/1/21', 'until 2021-01-01'],
    ['until 01/1/45', 'until 2045-01-01'],
    ['until 2/2/1', 'until 2001-02-02'],
    ['until 15/04/2005', 'until 2005-04-15'],
    ['until 16/5/2006', 'until 2006-05-16'],
    ['until 5/11/06', 'until 2006-11-05'],
    ['until 2006-5-1', 'until 2006-05-01'],
    ['until 6-5-22', 'until 2006-05-22'],
    ['until 04-12-1', 'until 2004-12-01'],
    ['everyday', 'every monday, tuesday, wednesday, thursday, friday, saturday, sunday'],
    ['a', '1'],
    ['an', '1'],
    ['tomorrow for an hour', '2021-01-02 for 1 hour'],
]);

beforeEach(function () {
    $this->preprocessor = new Preprocessor();
});

it('processes', function (string $code, string $result) {
    Carbon::setTestNow('2021-01-01 00:00:00');
    expect($this->preprocessor->preprocess($code, Carbon::now()))->toBe($result);
})->with('processed');
