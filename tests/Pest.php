<?php

use Carbon\Carbon;

expect()->extend('toMatchPeriod', function (string $start, string $end) {
    expect($this->value->start->eq(Carbon::parse($start)))->toBeTrue();
    expect($this->value->end->eq(Carbon::parse($end)))->toBeTrue();
});
