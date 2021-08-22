<?php

use Carbon\Carbon;
use Felix\Nest\Preprocessor;

dataset('processed', [
    ['6', '06:00pm'],
    ['6:00', '06:00pm'],
    ['7pm', '07:00pm'],
    ['6am', '06:00am'],
    ['06', '06:00pm'],
    ['06:0am', '06:00am'],
    ['12am', '12:00am'],
    ['12:1pm', '12:01pm'],
    ['1:00am', '01:00am'],
    ['0pm', '00:00pm'],
    ['1/1/21', '01/01/2021'],
    ['01/1/45', '01/01/2045'],
    ['2/2/1', '02/02/2001'],
    ['04/15/2005', '04/15/2005'],
    ['5/16/2006', '05/16/2006'],
    ['5/16/06', '05/16/2006'],
]);

it('processes correctly', function (string $current, string $processed) {
    $code = (new Preprocessor())->preprocess($current, Carbon::now());

    expect($code->getSymbol(0))->toBe($processed);
})->with('processed');
