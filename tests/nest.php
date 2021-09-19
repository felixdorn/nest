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
            'label'     => 'something',
            'starts_at' => '2021-01-02 05:00:00',
            'ends_at'   => '2021-01-02 06:00:00',
        ],
        [
            'label'     => 'something',
            'starts_at' => '2021-01-03 05:00:00',
            'ends_at'   => '2021-01-03 06:00:00',
        ],
    ]],
]);

it('works', function ($code, array $expectedOutput) {
    Carbon::setTestNow(Carbon::parse('2021-01-01 00:00:00'));

    $now = Carbon::now();

    $output = Nest::compile(
        $code,
        CarbonPeriod::create(
            $now,
            $now->clone()->addWeek()
        ),
        $now
    );

    expect($output)->toBe($expectedOutput);
})->with('input');
