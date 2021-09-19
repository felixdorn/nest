<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Felix\Nest\Nest;

//    ['"eat" every monday at 20 for 45 minutes
//"sleep" every monday at 22 for 2 hours
//"wake up" every monday at 7 for a minute until 1/1/2100', [
//        ['starts']
//    ]],
dataset('input', [
    ['"something" every weekend for an hour at 5', [
        [
            'starts_at' => '2021-01-02 05:00',
            'ends_at'   => '2021-01-02 06:00',
        ],
        [
            'starts_at' => '2021-01-03 05:00',
            'ends_at'   => '2021-01-03 06:00',
        ],
    ]],
    ['every monday, wednesday, friday and sunday at 22 for an hour between 15/04/2005 and 16/05/2006', [
        [
            'starts_at' => '2005-04-15 22:00',
            'ends_at'   => '2005-04-15 23:00',
        ],
        [
            'starts_at' => '2005-04-17 22:00',
            'ends_at'   => '2005-04-17 23:00',
        ],
        [
            'starts_at' => '2005-04-18 22:00',
            'ends_at'   => '2005-04-18 23:00',
        ],
        [
            'starts_at' => '2005-04-20 22:00',
            'ends_at'   => '2005-04-20 23:00',
        ],
        [
            'starts_at' => '2005-04-22 22:00',
            'ends_at'   => '2005-04-22 23:00',
        ],
    ], '2005-04-15 00:00:00'],
    ['"something" once 1/1/2021 from 15:00 to 16:00', [
        [
            'starts_at' => '2021-01-01 15:00',
            'ends_at'   => '2021-01-01 16:00',
        ],
    ]],
    ['"something else" in an hour for 30 minutes', [
        [
            'starts_at' => '2021-01-01 01:00',
            'ends_at'   => '2021-01-01 01:30',
        ],
    ]],
]);

it('works', function ($code, array $expectedOutput, ?string $now = null) {
    Carbon::setTestNow(Carbon::parse($now ?? '2021-01-01 00:00:00'));

    $now = Carbon::now();

    $event = Nest::compile(
        $code,
        CarbonPeriod::create(
            $now,
            $now->clone()->addWeek()
        ),
        $now
    );

    expect($event->getOccurrences())->toBe($expectedOutput);
})->with('input');
