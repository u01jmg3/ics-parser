# PHP ICS Parser

[![Latest Stable Version](https://poser.pugx.org/johngrogg/ics-parser/v/stable.png "Latest Stable Version")](https://packagist.org/packages/johngrogg/ics-parser)
[![Total Downloads](https://poser.pugx.org/johngrogg/ics-parser/downloads.png "Total Downloads")](https://packagist.org/packages/johngrogg/ics-parser)
[![Reference Status](https://www.versioneye.com/php/johngrogg:ics-parser/reference_badge.svg?style=flat "Reference Status")](https://www.versioneye.com/php/johngrogg:ics-parser/references)
[![Dependency Status](https://www.versioneye.com/php/johngrogg:ics-parser/badge.svg "Dependency Status")](https://www.versioneye.com/php/johngrogg:ics-parser)

---

## Installation

### Requirements
 - PHP 5 (≥ 5.3.0)

### Setup

 - Install [Composer](http://getcomposer.org)
   - Add the following requirement to `composer.json`
     - :warning: **Note the owner is `johngrogg` and not `u01jmg3`**

   ```yaml
   {
       "require": {
           "johngrogg/ics-parser": "dev-master"
       }
   }
   ```

---

## API

### `ICal` API

#### Constants

| Name               | Description                                 |
|--------------------|---------------------------------------------|
| `DATE_TIME_FORMAT` | Default datetime format to use              |
| `DEFAULT_TIMEZONE` | Default timezone to use                     |
| `RECURRENCE_EVENT` | Used to isolate generated recurrence events |
| `TIME_FORMAT`      | Default time format to use                  |
| `UNIX_MIN_YEAR`    | Minimum UNIX year to use                    |

#### Variables

| Name                     | Description                                                        | Configurable       | Default Value |
|--------------------------|--------------------------------------------------------------------|:------------------:|---------------|
| `$eventCount`            | Track the number of events in the current iCal feed                |         :x:        | N/A           |
| `$freebusyCount`         | Track the free/busy count in the current iCal feed                 |         :x:        | N/A           |
| `$todoCount`             | Track the number of todos in the current iCal feed                 |         :x:        | N/A           |
| `$defaultSpan`           | The value in years to use for indefinite, recurring events         | :white_check_mark: | `2`           |
| `$defaultWeekStart`      | The two letter representation of the first day of the week         | :white_check_mark: | `MO`          |
| `$skipRecurrence`        | Toggle whether to skip the parsing recurrence rules                | :white_check_mark: | `false`       |
| `$useTimeZoneWithRRules` | Toggle whether to use time zone info when parsing recurrence rules | :white_check_mark: | `false`       |

#### Functions

| Function                      | Parameter(s)                               | Visibility  | Description                                                                                                                   |
|-------------------------------|--------------------------------------------|-------------|-------------------------------------------------------------------------------------------------------------------------------|
| `initLines`                   | `$lines`                                   | `protected` | Initialises lines from a file                                                                                                 |
| `initString`                  | `$string`                                  | `protected` | Initialises lines from a string                                                                                               |
| `initUrl`                     | `$url`                                     | `protected` | Initialises lines from a URL                                                                                                  |
| `cleanData`                   | `$data`                                    | `protected` | Replace curly quotes and other special characters with their standard equivalents                                             |
| `convertDayOrdinalToPositive` | `$dayNumber`, `$weekday`, `$timestamp`     | `protected` | Convert a negative day ordinal to its equivalent positive form                                                                |
| `isValidTimeZoneId`           | `$timeZone`                                | `protected` | Check if a timezone is valid                                                                                                  |
| `numberOfDays`                | `$days`, `$start`, `$end`                  | `protected` | Get the number of days between a start and end date                                                                           |
| `parseDuration`               | `$date`, `$duration`                       | `protected` | Parse a duration and apply it to a date                                                                                       |
| `processDateConversions`      | -                                          | `protected` | Add fields `DTSTART_tz` and `DTEND_tz` to each Event                                                                          |
| `processEvents`               | -                                          | `protected` | Performs some admin tasks on all events as taken straight from the ics file.                                                  |
| `processRecurrences`          | -                                          | `protected` | Processes recurrence rules                                                                                                    |
| `removeUnprintableChars`      | `$data`                                    | `protected` | Remove unprintable ASCII and UTF-8 characters                                                                                 |
| `unfold`                      | `$string`                                  | `protected` | Unfold an iCal string in preparation for parsing                                                                              |
| `calendarDescription`         | -                                          | `public`    | Returns the calendar description                                                                                              |
| `calendarName`                | -                                          | `public`    | Returns the calendar name                                                                                                     |
| `calendarTimeZone`            | -                                          | `public`    | Returns the calendar timezone                                                                                                 |
| `events`                      | -                                          | `public`    | Returns an array of EventObjects. Every event is a class with the event details being properties within it.                   |
| `eventsFromInterval`          | `$interval`                                | `public`    | Returns a sorted array of the events following a given string, or false if no events exist in the range.                      |
| `eventsFromRange`             | `$rangeStart = false`, `$rangeEnd = false` | `public`    | Returns a sorted array of the events in a given range, or an empty array if no events exist in the range.                     |
| `freeBusyEvents`              | -                                          | `public`    | Returns an array of arrays with all free/busy events. Every event is an associative array and each property is an element it. |
| `hasEvents`                   | -                                          | `public`    | Returns a boolean value whether the current calendar has events or not                                                        |
| `iCalDateToUnixTimestamp`     | `$icalDate`                                | `public`    | Return Unix timestamp from iCal date time format                                                                              |
| `iCalDateWithTimeZone`        | `$event`, `$key`, `$forceTimeZone`         | `public`    | Return a date adapted to the calendar timezone depending on the event TZID                                                    |
| `sortEventsWithOrder`         | `$events`, `$sortOrder = SORT_ASC`         | `public`    | Sort events based on a given sort order                                                                                       |

---

### `EventObject` API

#### Functions

| Function    | Parameter(s)              | Description                                                        |
|-------------|---------------------------|--------------------------------------------------------------------|
| `printData` | `$html = '<p>%s: %s</p>'` | Return Event data excluding anything blank within an HTML template |

---

## Credits
 - [Jonathan Goode](https://github.com/u01jmg3) (programming, bug fixing, enhancement, coding standard)
 - [John Grogg](john.grogg@gmail.com) (programming, addition of event recurrence handling)
 - Frank Gregor (programming, feedback, testing)
 - [Martin Thoma](info@martin-thoma.de) (programming, bug fixing, project management, initial concept)

---

## Tools for Testing

 - [https://jakubroztocil.github.io/rrule/](https://jakubroztocil.github.io/rrule/)
 - [http://www.unixtimestamp.com/](http://www.unixtimestamp.com/)