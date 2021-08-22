# Nest

Nest is a simple pseudo-programming language for defining repeatable and non-repeatable events in time.

Here are a couple examples of the syntax:

```
"brush my teeth" everyday at 7am for 3 minutes
"dentist and chill" tomorrow at 5:30PM for an hour
"workout" every monday, saturday and sunday at 2 for 1h until 15/6/2021
```

Nest outputs a list of time periods with their labels.

Here's an example output for `"something" once 1/1/2021 from 3PM to 4PM`:

```json
{
    "label": "something",
    "occurrences": {
        "01/01/2021 03:00PM": "01/01/2020 04:00PM"
    }
}
```

## Reproducibility

The same code could lead to a completely different output based on the current time.

Therefore, when storing Nest code, you should also store the current time if reproducibility is an issue for you (it
probably is).

## Keywords

### Every

Every indicates that an event is repeated it takes as a parameter one or many weekdays.

```
every monday and saturday
```

See how lists work [here](#lists).

### For

For indicates for how long an event lasts.

```
for one hour
```

Here's a guide on [How you can quantify time in Nest](#quantifying-time)

Here's a list of all the time measurement units you may use:

* minute
* hour
* day
* week

You may pluralize them to keep the sentence grammatically correct but the compiler won't pick up on it if you don't.

Shorthands such as `1h` (1 hour) are also allowed.

Here's a guide on all available shorthands:

* m: minute
* min: minute
* h: hour
* d: day
* w: week

## Between

Between constrains the event between two dates.

```
between 12/04/2021 and 12/12/2021
```

If you wish to constrain an event between a time range, use [from](#from--to-).

## From ... to ...

From constrains the event between a time range.

```
from 11PM to 12:05PM
```

## Until

Until is a shorthand for the [between](#between) keyword.

```
until 12/12/2021
```

The start date is the current time.

## At

At defines at which time an event start. It is often used in combination with `for` that sets the duration of the event.

```
at 6 for an hour
```

## Lists

A list contains one or many literals such as `monday` or `1:00AM` and these are seperated with commas or the word `and`.

```
monday, saturday and sunday
1:00AM,4:00PM
tuesday and sunday, monday
monday, saturday, sunday
```

## Quantifying Time

You can use any number from `one` to a `hundred` in literal form.

```
for fifty-five minutes
```

Note: You may omit the hyphen and write things such as `thirty two` instead of `thirty-two` like a gangster.

You can use any non-negative integer such as `1` or `42`.

```
for 10 hours
```

To represent one unit of time, you may use a simpler form:

```
for an hour
for a day
```

> The compiler doesn't make a difference if you write `a` or `an` so `for a hour` still represents `1 hour` even though it is grammatically incorrect.

You can **not** do math and write things such as `for 5 + 2 minutes`.

### Implicit Time Quantification

When no period is explicitly given, Nest will always use PM.

```
at 6 => at 6:00PM
```

12AM will be interpreted midnight.

```
12AM => 00:00 (in 24-hour format)
```

12PM will be interpreted as noon.

```
12PM => 12:00 (in 24-hour format)
```
