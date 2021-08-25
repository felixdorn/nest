<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Felix\Nest\Lexer;
use Felix\Nest\Preprocessor;

beforeEach(function () {
    $this->preprocessor = new Preprocessor();
    $this->lexer = new Lexer();
    $this->generator = new \Felix\Nest\Generator();

    Carbon::setTestNow(
        Carbon::parse('2021-01-01 00:00:00')
    );
});

dataset('generated', [
    ['every monday at 12 for an hour', [
        'label'       => null,
        'now'         => '2021-01-01 00:00:00',
        'occurrences' => [
            ['starts_at' => '2021-01-04 12:00:00', 'ends_at' => '2021-01-04 13:00:00'],
        ],
    ]],
    ['"dentist appointment" 5/1/2021 from 17:30 to 18:15', [
        'label'       => 'dentist appointment',
        'now'         => '2021-01-01 00:00:00',
        'occurrences' => [
            ['starts_at' => '2021-01-05 17:30:00', 'ends_at' => '2021-01-05 18:15:00'],
        ],
    ]],
    ['every monday, wednesday, friday and sunday at 22 for an hour between 15/04/2005 and 16/05/2006', [
        'label'       => null,
        'now'         => '2005-04-15 00:00:00',
        'occurrences' => [
            ['starts_at' =>'2005-04-15 22:00:00', 'ends_at' => '2005-04-15 23:00:00'],
            ['starts_at' =>'2005-04-17 22:00:00', 'ends_at' => '2005-04-17 23:00:00'],
            ['starts_at' =>'2005-04-18 22:00:00', 'ends_at' => '2005-04-18 23:00:00'],
            ['starts_at' =>'2005-04-20 22:00:00', 'ends_at' => '2005-04-20 23:00:00'],
            ['starts_at' =>'2005-04-22 22:00:00', 'ends_at' => '2005-04-22 23:00:00'],
        ],
    ]],
    ['"something" once 1/1/2021 from 15:00 to 16:00', [
        'label'       => 'something',
        'now'         => '2021-01-01 00:00:00',
        'occurrences' => [
            ['starts_at' => '2021-01-01 15:00:00', 'ends_at' => '2021-01-01 16:00:00'],
        ],
    ]],
]);

it('generates', function (string $code, array $output) {
    Carbon::setTestNow(Carbon::parse($output['now']));
    $now = Carbon::now();
    expect(
        $this->generator->generate(
            $this->lexer->tokenize(
                $this->preprocessor->preprocess($code, $now),
                $now
            ),
            $now,
            CarbonPeriod::create($now, $now->clone()->addWeek())
        )
    )->toBe($output);
})->with('generated');
