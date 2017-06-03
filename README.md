# PHP ICS Parser

[![Latest Stable Version](https://poser.pugx.org/johngrogg/ics-parser/v/stable.png "Latest Stable Version")](https://packagist.org/packages/johngrogg/ics-parser)
[![Total Downloads](https://poser.pugx.org/johngrogg/ics-parser/downloads.png "Total Downloads")](https://packagist.org/packages/johngrogg/ics-parser)
[![Reference Status](https://www.versioneye.com/php/johngrogg:ics-parser/reference_badge.svg?style=flat "Reference Status")](https://www.versioneye.com/php/johngrogg:ics-parser/references)
[![Dependency Status](https://www.versioneye.com/php/johngrogg:ics-parser/badge.svg "Dependency Status")](https://www.versioneye.com/php/johngrogg:ics-parser)

---

## Installation

### Requirements
 - PHP 5 (≥ 5.3.0)
 - Valid ICS (`.ics`, `.ical`, `.ifb`) file

### Setup

 - Install [Composer](http://getcomposer.org)
   - Add the following requirement to `composer.json`
     - :warning: **Note the owner is `johngrogg` and not `u01jmg3`**
   - If you want to try out newer features then require [`dev-master`](https://getcomposer.org/doc/articles/aliases.md#branch-alias)

   ```yaml
   {
       "require": {
           "johngrogg/ics-parser": "^2"
       }
   }
   ```

### How to instantiate the Parser

- Using the example script as a guide, [refer to this code](https://github.com/u01jmg3/ics-parser/blob/master/examples/index.php#L1-L19)

---

## API

### `ICal` API

#### Constants

| Name                | Description                                 |
|---------------------|---------------------------------------------|
| `DATE_TIME_FORMAT`  | Default datetime format to use              |
| `RECURRENCE_EVENT`  | Used to isolate generated recurrence events |
| `SECONDS_IN_A_WEEK` | Integer of the number of seconds in a week  |
| `TIME_FORMAT`       | Default time format to use                  |
| `UNIX_MIN_YEAR`     | Minimum Unix year to use                    |

#### Variables

| Name                     | Description                                                        | Configurable       | Default Value  |
|--------------------------|--------------------------------------------------------------------|:------------------:|----------------|
| `$cal`                   | The parsed calendar                                                |         :x:        | N/A            |
| `$eventCount`            | Track the number of events in the current iCal feed                |         :x:        | N/A            |
| `$freeBusyCount`         | Track the free/busy count in the current iCal feed                 |         :x:        | N/A            |
| `$todoCount`             | Track the number of todos in the current iCal feed                 |         :x:        | N/A            |
| `$defaultSpan`           | The value in years to use for indefinite, recurring events         | :white_check_mark: | `2`            |
| `$defaultTimeZone`       | Customise the default time zone used by the parser                 | :white_check_mark: | System default |
| `$defaultWeekStart`      | The two letter representation of the first day of the week         | :white_check_mark: | `MO`           |
| `$skipRecurrence`        | Toggle whether to skip the parsing recurrence rules                | :white_check_mark: | `false`        |
| `$useTimeZoneWithRRules` | Toggle whether to use time zone info when parsing recurrence rules | :white_check_mark: | `false`        |

#### Methods

| Method                        | Parameter(s)                                               | Visibility  | Description                                                                                                                   |
|-------------------------------|------------------------------------------------------------|-------------|-------------------------------------------------------------------------------------------------------------------------------|
| `__construct`                 | `$files = false`, `$options = array()`                     | `public`    | Creates the ICal object                                                                                                       |
| `initFile`                    | `$file`                                                    | `protected` | Initialises lines from a file                                                                                                 |
| `initLines`                   | `$lines`                                                   | `protected` | Initialises the parser using an array containing each line of iCal content                                                    |
| `initString`                  | `$string`                                                  | `protected` | Initialises lines from a string                                                                                               |
| `initUrl`                     | `$url`                                                     | `protected` | Initialises lines from a URL                                                                                                  |
| `cleanData`                   | `$data`                                                    | `protected` | Replace curly quotes and other special characters with their standard equivalents                                             |
| `convertDayOrdinalToPositive` | `$dayNumber`, `$weekday`, `$timestamp`                     | `protected` | Convert a negative day ordinal to its equivalent positive form                                                                |
| `fileOrUrl`                   | `$filename`                                                | `protected` | Reads an entire file or URL into an array                                                                                     |
| `isFileOrUrl`                 | `$filename`                                                | `protected` | Check if filename exists as a file or URL                                                                                     |
| `isValidTimeZoneId`           | `$timeZone`                                                | `protected` | Check if a time zone is valid                                                                                                 |
| `mb_str_replace`              | `$search`, `$replace`, `$subject`, `$count = 0`            | `protected` | Replace all occurrences of the search string with the replacement string. Multibyte safe.                                     |
| `numberOfDays`                | `$days`, `$start`, `$end`                                  | `protected` | Get the number of days between a start and end date                                                                           |
| `parseDuration`               | `$date`, `$duration`, `$format = 'U'`                      | `protected` | Parse a duration and apply it to a date                                                                                       |
| `processDateConversions`      | -                                                          | `protected` | Add fields `DTSTART_tz` and `DTEND_tz` to each Event                                                                          |
| `processEvents`               | -                                                          | `protected` | Performs some admin tasks on all events as taken straight from the ics file.                                                  |
| `processRecurrences`          | -                                                          | `protected` | Processes recurrence rules                                                                                                    |
| `removeUnprintableChars`      | `$data`                                                    | `protected` | Remove unprintable ASCII and UTF-8 characters                                                                                 |
| `unfold`                      | `$lines`                                                   | `protected` | Unfold an iCal file in preparation for parsing                                                                                |
| `calendarDescription`         | -                                                          | `public`    | Returns the calendar description                                                                                              |
| `calendarName`                | -                                                          | `public`    | Returns the calendar name                                                                                                     |
| `calendarTimeZone`            | `$ignoreUtc`                                               | `public`    | Returns the calendar time zone                                                                                                |
| `events`                      | -                                                          | `public`    | Returns an array of Events. Every event is a class with the event details being properties within it.                         |
| `eventsFromInterval`          | `$interval`                                                | `public`    | Returns a sorted array of the events following a given string, or false if no events exist in the range.                      |
| `eventsFromRange`             | `$rangeStart = false`, `$rangeEnd = false`                 | `public`    | Returns a sorted array of the events in a given range, or an empty array if no events exist in the range.                     |
| `freeBusyEvents`              | -                                                          | `public`    | Returns an array of arrays with all free/busy events. Every event is an associative array and each property is an element it. |
| `hasEvents`                   | -                                                          | `public`    | Returns a boolean value whether the current calendar has events or not                                                        |
| `iCalDateToDateTime`          | `$icalDate`, `$forceTimeZone = false`, `$forceUtc = false` | `public`    | Return a DateTime object from an iCal date time format                                                                        |
| `iCalDateToUnixTimestamp`     | `$icalDate`, `$forceTimeZone = false`, `$forceUtc = false` | `public`    | Return a Unix timestamp from an iCal date time format                                                                         |
| `iCalDateWithTimeZone`        | `$event`, `$key`, `$forceTimeZone`                         | `public`    | Return a date adapted to the calendar time zone depending on the event TZID                                                   |
| `isValidDate`                 | `$value`                                                   | `public`    | Check if a date string is a valid date                                                                                        |
| `parseExdates`                | `$event`                                                   | `public`    | Parse a list of excluded dates to be applied to an Event                                                                      |
| `sortEventsWithOrder`         | `$events`, `$sortOrder = SORT_ASC`                         | `public`    | Sort events based on a given sort order                                                                                       |

---

### `Event` API

#### Constants

| Name                | Description                                                   |
|---------------------|---------------------------------------------------------------|
| `TIMEZONE_TEMPLATE` | `sprintf` template for use with `updateEventTimeZoneString()` |

#### Methods

| Method                      | Parameter(s)                                | Visibility  | Description                                                                                             |
|-----------------------------|---------------------------------------------|-------------|---------------------------------------------------------------------------------------------------------|
| `__construct`               | `$data = array()`                           | `public`    | Creates the Event object                                                                                |
| `printData`                 | `$html = '<p>%s: %s</p>'`                   | `public`    | Return Event data excluding anything blank within an HTML template                                      |
| `prepareData`               | `$value`                                    | `protected` | Prepares the data for output                                                                            |
| `snakeCase`                 | `$input`, `$glue = '_'`, `$separator = '-'` | `protected` | Convert the given input to snake_case                                                                   |
| `updateEventTimeZoneString` |                                             | `protected` | Extend `{DTSTART|DTEND|RECURRENCE-ID}_array` to include `TZID=Timezone:YYYYMMDD[T]HHMMSS` of each event |

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