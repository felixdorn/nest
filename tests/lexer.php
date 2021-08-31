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
        'when'     => [['every', 'monday']],
        'at'       => '00:15',
        'duration' => 15 * TimeUnit::HOUR + 45 * TimeUnit::MINUTE,
    ]],
    ['everyday for an hour at 6AM', [
        'when'     => [['every', 'monday'], ['every', 'tuesday'], ['every', 'wednesday'], ['every', 'thursday'], ['every', 'friday'], ['every', 'saturday'], ['every', 'sunday']],
        'duration' => TimeUnit::HOUR,
        'at'       => '06:00',
    ]],
    ['every wednesday at 15:05 for 55 minutes', [
        'when'     => [['every', 'wednesday']],
        'at'       => '15:05',
        'duration' => 55 * TimeUnit::MINUTE,
    ]],
    ['every monday, wednesday, friday and sunday at 22 for an hour between 15/04/2005 and 16/05/2006', [
        'when'      => [['every', 'monday'], ['every', 'wednesday'], ['every', 'friday'], ['every', 'sunday']],
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
        'when'      => [['every', 'monday'], ['every', 'sunday'], ['every', 'saturday'], ['every', 'tuesday']],
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
        'when'      => [['every', 'monday']],
        'at'        => '15:00',
        'duration'  => TimeUnit::HOUR,
    ]],
    ['every MondAY, SATURDAY and sunday ', [
        'when' => [['every', 'monday'], ['every', 'saturday'], ['every', 'sunday']],
    ]],
    ['once 1/1/2021 from 15:00 to 16:00', [
        'when'     => [['once', '2021-01-01']],
        'at'       => '15:00',
        'duration' => TimeUnit::HOUR,
    ]],
    ['12/08/2021 for 2 minutes at 21:30', [
        'when'     => [['once', '2021-08-12']],
        'duration' => 2 * TimeUnit::MINUTE,
        'at'       => '21:30',
    ]],
    ['"dentist appointment" 15/05/2005 for an hour', [
        'label'    => 'dentist appointment',
        'when'     => [['once', '2005-05-15']],
        'duration' => TimeUnit::HOUR,
    ]],
    ['"hello \"world\""', [
        'label' => 'hello "world"',
    ]],
    ['in an hour', [
        'when' => [['once', '2021-01-01']],
        'at'   => '01:00',
    ]],
    ['in 5 days', [
        'when' => [['once', '2021-01-06']],
    ]],
    ['for 5 days', [
        'duration' => 5 * TimeUnit::DAY,
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
})->with('compilations')->skip();
