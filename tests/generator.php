<?php

use Carbon\Carbon;
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
        'label'       => '',
        'now'         => '2021-01-01 00:00:00',
        'occurrences' => [],
    ]],
]);

it('generates', function (string $code, array $output) {
    $now = Carbon::now();
    expect(
        $this->generator->generate(
            $this->lexer->tokenize(
                $this->preprocessor->preprocess($code, $now)
            ),
            $now
        )
    )->toBe($output);
})->with('generated');
