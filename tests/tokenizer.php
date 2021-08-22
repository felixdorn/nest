<?php

use Felix\Nest\Compiler\Tokenizer;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    $this->tokenizer = new Tokenizer();
    testTime()->freeze('2021-01-01 12:00:00');
});

it('compiles', function () {
    expect($this->tokenizer->tokenize('for 1 hour at 3:30PM every monday, sunday,saturday and tuesday until 01/02/2021')['when'])->toBe([
        'monday', 'sunday', 'saturday', 'tuesday',
    ]);

    expect($this->tokenizer->tokenize('for 1 hour at 6:30PM'))->toBe([
        'constraints' => [
            'duration' => 3600.0,
            'at'       => '6:30PM',
        ],
    ]);

    expect($this->tokenizer->tokenize('everyday for 1 hour'))->toBe([
        'constraints' => [
            'duration' => 3600.0,
        ],
        'when' => [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        ],
    ]);

//    expect($this->tokenizer->tokenize('"coiffeur" tomorrow at 6'))->toBe([]);

    expect($this->tokenizer->tokenize('"mama\" mia" between 17/04/2022 and 19/07/2022 every monday at 3:00PM for 1 hour'))->toBe([
        'constraints' => [
            'between'  => ['17/04/2022', '19/07/2022'],
            'at'       => '15:00',
            'duration' => 3600.0,
        ],
        'label' => 'mama" mia',
        'when'  => ['monday'],
    ]);
});
