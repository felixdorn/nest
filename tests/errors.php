<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Felix\Nest\Exceptions\CompileErrorException;
use Felix\Nest\Nest;

dataset('invalidCode', [
    ['until between', 'Invalid date: between'],
    ['something random', 'Syntax error, unexpected something'],
    ['everyday for an hour at 4 until 2030/30/30', 'Invalid date: 2030/30/30'],
    /*
     * TODO: should show the real value
     */
    ['30/30/30', 'Syntax error, unexpected 2030-30-30'],
    ['every wolf', 'Invalid date: wolf'],
//    ['at 6 15/04/2005', 'Syntax error, unexpected 2005-04-15']
]);

it('throws an error', function (string $code, string $exceptionMessage) {
    $errorThrown = false;
    try {
        $event = Nest::compile($code, CarbonPeriod::create(
            Carbon::now(),
            Carbon::now()->addWeek()
        ), Carbon::now());
    } catch (CompileErrorException $e) {
        expect($e->getMessage())->toBe($exceptionMessage);

        $errorThrown = true;
    }

    expect($errorThrown)->toBe(true);
})->with('invalidCode');
