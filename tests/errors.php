<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Felix\Nest\Exceptions\CompileErrorException;
use Felix\Nest\Nest;

dataset('invalidCode', [
    ['something random', 'Syntax error, unexpected something'],
    ['everyday for an hour at 4 until 2004/1205/1451', 'Invalid date: 2030-30-30'],
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
})->with('invalidCode')->only();
