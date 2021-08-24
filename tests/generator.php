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

/*
 * TODO: The compiler needs a carbon period
 *
 * The min stuff with min($compilerMaxPeriod, $betweenMax)
 */
dataset('generated', [
    ['every monday at 12 for an hour', [
        'label'       => null,
        'now'         => '2021-01-01 00:00:00',
        'occurrences' => [
            ['2021-01-04 12:00:00', '2021-01-04 13:00:00'],
        ],
    ]],
    ['"dentist appointment" 15/04/2005 from 17:30 to 18:15', [
        'label'      => 'dentist appointment',
        'now'        => '2021-01-01 00:00:00',
        'occurences' => [
            ['2005-04-15 17:30', '2005-04-15 18:15'],
        ],
    ]],
]);

it('generates', function (string $code, array $output) {
    $now = Carbon::now();
    expect(
        $this->generator->generate(
            $this->lexer->tokenize(
                $this->preprocessor->preprocess($code, $now)
            ),
            $now,
            CarbonPeriod::create($now, $now->clone()->addWeek())
        )
    )->toBe($output);
})->with('generated');
