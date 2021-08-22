<?php

use Felix\Nest\Compiler\Preprocessor;

expect()->extend('toBePreprocessed', function (string $comparison) {
    $preprocessor = new Preprocessor();

    expect($preprocessor->process($this->value))->toBe($comparison);
});

it('works', function () {
    expect('"le coiffeur" at 6')->toBePreprocessed('"le coiffeur" at 6:00pm');
    expect('"le coiffeur" at 7AM')->toBePreprocessed('"le coiffeur" at 7:00am');
});
