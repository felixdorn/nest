<?php

use Felix\StructuredTime\Tokenizer;

beforeEach(function () {
    $this->tokenizer = new Tokenizer();
});

it('compiles', function () {
    expect($this->tokenizer->tokenize('for 1 hour at 18:30 every monday, sunday,saturday and tuesday until 1/1/21'))->toBe([]);

//    expect($this->tokenizer->tokenize('for 1 hour at 18h30'))->toBe([
//        'constraints' => [
//            'at'       => '18:30',
//            'duration' => 3600.0,
//        ],
//    ]);

    expect($this->tokenizer->tokenize('"mama\" mia" between 17/04/2022 and 19/07/2022 every monday at 15:00 for 1 hour'))->toBe([
        'constraints' => [
            'between'  => ['17/04/2022', '19/07/2022'],
            'at'       => '15:00',
            'duration' => 3600.0,
        ],
        'label' => 'mama" mia',
        'when'  => ['monday'],
    ]);
});
