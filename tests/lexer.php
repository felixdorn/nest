<?php

use Carbon\Carbon;
use Felix\Nest\Lexer;
use Felix\Nest\Preprocessor;
use Felix\Nest\Support\TimeUnit;

dataset('compilations', [
//    ['for half an hour', [
//        'duration' => TimeUnit::HOUR / 2,
//    ]],
    ['every monday from 12:15AM to 4PM', [
        'when'     => ['monday'],
        'at'       => '00:15',
        'duration' => 15 * TimeUnit::HOUR + 45 * TimeUnit::MINUTE,
    ]],
    ['everyday for an hour at 6AM', [
        'when'     => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
        'duration' => TimeUnit::HOUR,
        'at'       => '06:00',
    ]],
    ['every wednesday at 15:05 for 55 minutes', [
        'when'     => ['wednesday'],
        'at'       => '15:05',
        'duration' => 55 * TimeUnit::MINUTE,
    ]],
    ['every monday, wednesday, friday and sunday at 22 for an hour between 15/04/2005 and 16/05/2006', [
        'when'      => ['monday', 'wednesday', 'friday', 'sunday'],
        'at'        => '22:00',
        'duration'  => TimeUnit::HOUR,
        'starts_at' => '2005-04-15',
        'ends_at'   => '2006-05-16',
    ]],
    ['for two minutes', [
        'duration' => 2 * TimeUnit::MINUTE,
    ]],
    ['for 1 hour at 3:30PM every monday, sunday,saturday and tuesday until 01/02/2021', [
        'duration'  => TimeUnit::HOUR,
        'at'        => '15:30',
        'when'      => ['monday', 'sunday', 'saturday', 'tuesday'],
        'starts_at' => '2021-01-01 00:00:00',
        'ends_at'   => '2021-02-01',
    ]],
    ['for three hours at 6:30PM', [
        'duration' => 3 * TimeUnit::HOUR,
        'at'       => '18:30',
    ]],
    ['between 17/04/2022 and 19/07/2022 every monday at 3:00PM for 1 hour', [
        'starts_at' => '2022-04-17',
        'ends_at'   => '2022-07-19',
        'when'      => ['monday'],
        'at'        => '15:00',
        'duration'  => TimeUnit::HOUR,
    ]],
    ['every MondAY, SATURDAY and sunday ', [
        'when' => ['monday', 'saturday', 'sunday'],
    ]],
    ['once 1/1/2021 from 15:00 to 16:00', [
        'when'     => '2021-01-01',
        'at'       => '15:00',
        'duration' => TimeUnit::HOUR,
    ]],
    ['12/08/2021 for 2 minutes at 21:30', [
        'when'     => '2021-08-12',
        'duration' => 2 * TimeUnit::MINUTE,
        'at'       => '21:30',
    ]],
    ['"dentist appointment" 15/05/2005 for an hour', [
        'label'    => 'dentist appointment',
        'when'     => '2005-05-15',
        'duration' => TimeUnit::HOUR,
    ]],
]);

beforeEach(function () {
    $this->preprocessor = new Preprocessor();
    $this->lexer = new Lexer();
});

it('compiles', function (string $code, array $rawExpectedEvent) {
    Carbon::setTestNow('2021/01/01 00:00:00');

    $event = $this->lexer->tokenize(
        $this->preprocessor->preprocess($code, Carbon::now()),
        Carbon::now()
    );

    expect(array_filter($event->toArray()))->toBe(array_filter([
        'when'     => $rawExpectedEvent['when'] ?? '',
        'label'    => $rawExpectedEvent['label'] ?? '',
        'startsAt' => $rawExpectedEvent['starts_at'] ?? '',
        'endsAt'   => $rawExpectedEvent['ends_at'] ?? '',
        'at'       => $rawExpectedEvent['at'] ?? '',
        'duration' => $rawExpectedEvent['duration'] ?? '',
    ]));
})->with('compilations');
