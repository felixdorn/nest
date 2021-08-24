<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Felix\Nest\Exceptions\CompileErrorException;
use Felix\Nest\Nest;

dataset('invalidCode', [
    ['something random', 'Syntax error, unexpected something'],
    ['everyday for an hour at 4 until 2004/1205/1451', 'Invalid format for a date, must be either d/m/y or y-m-d'],
    ['30/30/30', 'Invalid format for a date, must be either d/m/y or y-m-d'],
    ['until between', 'Invalid format for a date, must be either d/m/y or y-m-d'],
    ['every wolf', 'Syntax error, unexpected weekday wolf'],
]);

it('throws an error', function (string $code, string $exceptionMessage) {
    $errorThrown = false;
    try {
        Nest::compile($code, CarbonPeriod::create(
            Carbon::now(),
            Carbon::now()->addWeek()
        ));
    } catch (CompileErrorException $e) {
        expect($e->getMessage())->toBe($exceptionMessage);

        $errorThrown = true;
    }

    expect($errorThrown)->toBe(true);
})->with('invalidCode');

it('throws an error when no boundaries are set for an infinitely repeatable event', function () {
    Nest::compile('every monday');
})->throws(CompileErrorException::class, 'No boundaries set for an infinitely repeatable event.');
