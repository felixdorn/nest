<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Felix\Nest\Exceptions\CompileErrorException;
use Felix\Nest\Nest;

dataset('invalidCode', [
    ['something random', 'Syntax error, unexpected something'],
    ['everyday for an hour at 4 until 2030/30/30', 'Invalid date: 2030/30/30'],
    /*
     * TODO: the semantic analyzer does not have access to the real value of the date
     * and the logic of the semantic analyzer should probably be moved to the parser.
     *
     * Implementing a symbol table could also help.
     */
    ['30/30/30', 'Invalid date: 2030-30-30'],
    ['until between', 'Invalid date: between'],
    ['every wolf', 'Invalid date: wolf'],
]);

it('throws an error', function (string $code, string $exceptionMessage) {
    $errorThrown = false;
    try {
        Nest::compile($code, CarbonPeriod::create(
            Carbon::now(),
            Carbon::now()->addWeek()
        ), Carbon::now());
    } catch (CompileErrorException $e) {
        expect($e->getMessage())->toBe($exceptionMessage);

        $errorThrown = true;
    }

    expect($errorThrown)->toBe(true);
})->with('invalidCode');
