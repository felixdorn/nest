<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Felix\Nest\Nest;

dataset('input', [
    ['"something" every weekend for an hour at 5', [
        'label'       => 'something',
        'now'         => '2021-01-01 00:00:00',
        'occurrences' => [
            ['starts_at' => '2021-01-02 05:00:00', 'ends_at' => '2021-01-02 06:00:00'],
            ['starts_at' => '2021-01-03 05:00:00', 'ends_at' => '2021-01-03 06:00:00'],
        ],
    ]],
//    ['"eat" every monday at 20 for 45 minutes
//"sleep" every monday at 22 for 2 hours
//"wake up" every monday at 7 for a minute until 1/1/2100', [
//        ['starts']
//    ]],
]);

it('works', function ($code, array $expectedOutput) {
    Carbon::setTestNow(Carbon::parse('2021-01-01 00:00:00'));

    $output = Nest::compile(
        $code,
        CarbonPeriod::create(
            Carbon::now(),
            Carbon::now()->addWeek()
        ),
        Carbon::now()
    );

    expect($output)->toBe($expectedOutput);
})->with('input');
