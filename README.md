# PHP ICS Parser

[![Latest Stable Release](https://poser.pugx.org/johngrogg/ics-parser/v/stable.png "Latest Stable Release")](https://packagist.org/packages/johngrogg/ics-parser)
[![Total Downloads](https://poser.pugx.org/johngrogg/ics-parser/downloads.png "Total Downloads")](https://packagist.org/packages/johngrogg/ics-parser)

---

## Installation

### Requirements
 - PHP 5 (≥ 5.3.9)
 - [Valid ICS](https://icalendar.org/validator.html) (`.ics`, `.ical`, `.ifb`) file
 - [IANA](https://www.iana.org/time-zones), [Unicode CLDR](http://cldr.unicode.org/translation/timezones) or [Windows](https://support.microsoft.com/en-ca/help/973627/microsoft-time-zone-index-values) Time Zones

### Setup

 - Install [Composer](https://getcomposer.org/)
   - Add the following dependency to `composer.json`
     - :warning: **Note with Composer the owner is `johngrogg` and not `u01jmg3`**
   - To access the latest stable branch (`v2`) use the following
     - To access new features you can require [`dev-master`](https://getcomposer.org/doc/articles/aliases.md#branch-alias)

       ```yaml
       {
           "require": {
               "johngrogg/ics-parser": "^2"
           }
       }
       ```

## Running tests

```sh
composer test
```

## How to use

### How to instantiate the Parser

 - Using the example script as a guide, [refer to this code](https://github.com/u01jmg3/ics-parser/blob/master/examples/index.php#L1-L22)

#### What will the parser return?

 - Each key/value pair from the iCal file will be parsed creating an associative array for both the calendar and every event it contains.
 - Also injected will be content under `dtstart_tz` and `dtend_tz` for accessing start and end dates with time zone data applied.
 - Where possible [`DateTime`](https://secure.php.net/manual/en/class.datetime.php) objects are used and returned.
   - :information_source: **Note the parser is limited to [relative date formats](https://www.php.net/manual/en/datetime.formats.relative.php) which can inhibit how complex recurrence rule parts are processed (e.g. `BYDAY` combined with `BYSETPOS`)**

   ```php
   // Dump the whole calendar
   var_dump($ical->cal);

   // Dump every event
   var_dump($ical->events());
   ```

 - Also included are special `{property}_array` arrays which further resolve the contents of a key/value pair.

   ```php
   // Dump a parsed event's start date
   var_dump($event->dtstart_array);

   // array (size=4)
   //   0 =>
   //     array (size=1)
   //       'TZID' => string 'America/Detroit' (length=15)
   //   1 => string '20160409T090000' (length=15)
   //   2 => int 1460192400
   //   3 => string 'TZID=America/Detroit:20160409T090000' (length=36)
   ```

---

## When Parsing an iCal Feed

Parsing [iCal/iCalendar/ICS](https://en.wikipedia.org/wiki/ICalendar) resources can pose several challenges. One challenge is that
the specification is a moving target; the original RFC has only been updated four times in ten years. The other challenge is that vendors
were both liberal (read: creative) in interpreting the specification and productive implementing proprietary extensions.

However, what impedes efficient parsing most directly are recurrence rules for events. This library parses the original
calendar into an easy to work with memory model. This requires that each recurring event is expanded or exploded. Hence,
a single event that occurs daily will generate a new event instance for each day as this parser processes the
calendar ([`$defaultSpan`](#variables) limits this). To get an idea how this is done take a look at the
[call graph](https://user-images.githubusercontent.com/624195/45904641-f3cd0a80-bded-11e8-925f-7bcee04b8575.png).

As a consequence the _entire_ calendar is parsed line-by-line, and thus loaded into memory, first. As you can imagine
large calendars tend to get huge when exploded i.e. with all their recurrence rules evaluated. This is exacerbated when
old calendars do not remove past events as they get fatter and fatter every year.

This limitation is particularly painful if you only need a window into the original calendar. It seems wasteful to parse
the entire fully exploded calendar into memory if you later are going to call the
[`eventsFromInterval()` or `eventsFromRange()`](#methods) on it.

In late 2018 [#190](https://github.com/u01jmg3/ics-parser/pull/190) added the option to drop all events outside a given
range very early in the parsing process at the cost of some precision (time zone calculations are not calculated at that point). This
massively reduces the total time for parsing a calendar. The same goes for memory consumption. The precondition is that
you know upfront that you don't care about events outside a given range.

Let's say you are only interested in events from yesterday, today and tomorrow. To compensate for the fact that the
tricky time zone transformations and calculations have not been executed yet by the time the parser has to decide whether
to keep or drop an event you can set it to filter for **+-2d** instead of +-1d. Once it is done you would then call
`eventsFromRange()` with +-1d to get precisely the events in the window you are interested in. That is what the variables
[`$filterDaysBefore` and `$filterDaysAfter`](#variables) are for.

In Q1 2019 [#213](https://github.com/u01jmg3/ics-parser/pull/213) further improved the performance by immediately
dropping _non-recurring_ events once parsed if they are outside that fuzzy window. This greatly reduces the maximum
memory consumption for large calendars. PHP by default does not allocate more than 128MB heap and would otherwise crash
with `Fatal error: Allowed memory size of 134217728 bytes exhausted`. It goes without saying that recurring events first
need to be evaluated before non-fitting events can be dropped.

---

## API

### `ICal` API

#### Variables

| Name                           | Configurable             | Default Value                                                                             | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   |
|--------------------------------|:------------------------:|-------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `$alarmCount`                  | :heavy_multiplication_x: | N/A                                                                                       | Tracks the number of alarms in the current iCal feed                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| `$cal`                         | :heavy_multiplication_x: | N/A                                                                                       | The parsed calendar                                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| `$defaultSpan`                 | :ballot_box_with_check:  | `2`                                                                                       | The value in years to use for indefinite, recurring events                                                                                                                                                                                                                                                                                                                                                                                                                                    |
| `$defaultTimeZone`             | :ballot_box_with_check:  | [System default](https://secure.php.net/manual/en/function.date-default-timezone-get.php) | Enables customisation of the default time zone                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| `$defaultWeekStart`            | :ballot_box_with_check:  | `MO`                                                                                      | The two letter representation of the first day of the week                                                                                                                                                                                                                                                                                                                                                                                                                                    |
| `$disableCharacterReplacement` | :ballot_box_with_check:  | `false`                                                                                   | Toggles whether to disable all character replacement. Will replace curly quotes and other special characters with their standard equivalents if `false`. Can be a costly operation!                                                                                                                                                                                                                                                                                                           |
| `$eventCount`                  | :heavy_multiplication_x: | N/A                                                                                       | Tracks the number of events in the current iCal feed                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| `$filterDaysAfter`             | :ballot_box_with_check:  | `null`                                                                                    | When set the parser will ignore all events more than roughly this many days _after_ now. To be on the safe side it is advised that you make the filter window `+/- 1` day larger than necessary. For performance reasons this filter is applied before any date and time zone calculations are done. Hence, depending the time zone settings of the parser and the calendar the cut-off date is not "calibrated". You can then use `$ical->eventsFromRange()` to precisely shrink the window. |
| `$filterDaysBefore`            | :ballot_box_with_check:  | `null`                                                                                    | When set the parser will ignore all events more than roughly this many days _before_ now. See `$filterDaysAfter` above for more details.                                                                                                                                                                                                                                                                                                                                                      |
| `$freeBusyCount`               | :heavy_multiplication_x: | N/A                                                                                       | Tracks the free/busy count in the current iCal feed                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| `$httpBasicAuth`               | :heavy_multiplication_x: | `array()`                                                                                 | Holds the username and password for HTTP basic authentication                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| `$httpUserAgent`               | :heavy_multiplication_x: | `null`                                                                                    | Holds the custom User Agent string header                                                                                                                                                                                                                                                                                                                                                                                                                                                     |
| `$httpAcceptLanguage`          | :heavy_multiplication_x: | `null`                                                                                    | Holds the custom Accept Language request header, e.g. "en" or "de"                                                                                                                                                                                                                                                                                                                                                                                                                            |
| `$shouldFilterByWindow`        | :heavy_multiplication_x: | `false`                                                                                   | `true` if either `$filterDaysBefore` or `$filterDaysAfter` are set                                                                                                                                                                                                                                                                                                                                                                                                                            |
| `$skipRecurrence`              | :ballot_box_with_check:  | `false`                                                                                   | Toggles whether to skip the parsing of recurrence rules                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| `$todoCount`                   | :heavy_multiplication_x: | N/A                                                                                       | Tracks the number of todos in the current iCal feed                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| `$windowMaxTimestamp`          | :heavy_multiplication_x: | `null`                                                                                    | If `$filterDaysBefore` or `$filterDaysAfter` are set then the events are filtered according to the window defined by this field and `$windowMinTimestamp`                                                                                                                                                                                                                                                                                                                                     |
| `$windowMinTimestamp`          | :heavy_multiplication_x: | `null`                                                                                    | If `$filterDaysBefore` or `$filterDaysAfter` are set then the events are filtered according to the window defined by this field and `$windowMaxTimestamp`                                                                                                                                                                                                                                                                                                                                     |

#### Methods

| Method                                          | Parameter(s)                                                                                  | Visibility  | Description                                                                                                                                                      |
|-------------------------------------------------|-----------------------------------------------------------------------------------------------|-------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `__construct`                                   | `$files = false`, `$options = array()`                                                        | `public`    | Creates the ICal object                                                                                                                                          |
| `initFile`                                      | `$file`                                                                                       | `protected` | Initialises lines from a file                                                                                                                                    |
| `initLines`                                     | `$lines`                                                                                      | `protected` | Initialises the parser using an array containing each line of iCal content                                                                                       |
| `initString`                                    | `$string`                                                                                     | `protected` | Initialises lines from a string                                                                                                                                  |
| `initUrl`                                       | `$url`, `$username = null`, `$password = null`, `$userAgent = null`, `$acceptLanguage = null` | `protected` | Initialises lines from a URL. Accepts a username/password combination for HTTP basic authentication, a custom User Agent string and the accepted client language |
| `addCalendarComponentWithKeyAndValue`           | `$component`, `$keyword`, `$value`                                                            | `protected` | Add one key and value pair to the `$this->cal` array                                                                                                             |
| `calendarDescription`                           | -                                                                                             | `public`    | Returns the calendar description                                                                                                                                 |
| `calendarName`                                  | -                                                                                             | `public`    | Returns the calendar name                                                                                                                                        |
| `calendarTimeZone`                              | `$ignoreUtc`                                                                                  | `public`    | Returns the calendar time zone                                                                                                                                   |
| `cleanData`                                     | `$data`                                                                                       | `protected` | Replaces curly quotes and other special characters with their standard equivalents                                                                               |
| `eventsFromInterval`                            | `$interval`                                                                                   | `public`    | Returns a sorted array of events following a given string, or `false` if no events exist in the range                                                            |
| `eventsFromRange`                               | `$rangeStart = false`, `$rangeEnd = false`                                                    | `public`    | Returns a sorted array of events in a given range, or an empty array if no events exist in the range                                                             |
| `events`                                        | -                                                                                             | `public`    | Returns an array of Events                                                                                                                                       |
| `fileOrUrl`                                     | `$filename`                                                                                   | `protected` | Reads an entire file or URL into an array                                                                                                                        |
| `filterValuesUsingBySetPosRRule`                | `$bysetpos`, `$valueslist`                                                                    | `protected` | Filters a provided values-list by applying a BYSETPOS RRule                                                                                                      |
| `freeBusyEvents`                                | -                                                                                             | `public`    | Returns an array of arrays with all free/busy events                                                                                                             |
| `getDaysOfMonthMatchingByDayRRule`              | `$bydays`, `$initialDateTime`                                                                 | `protected` | Find all days of a month that match the BYDAY stanza of an RRULE                                                                                                 |
| `getDaysOfMonthMatchingByMonthDayRRule`         | `$byMonthDays`, `$initialDateTime`                                                            | `protected` | Find all days of a month that match the BYMONTHDAY stanza of an RRULE                                                                                            |
| `getDaysOfYearMatchingByDayRRule`               | `$byDays`, `$initialDateTime`                                                                 | `protected` | Find all days of a year that match the BYDAY stanza of an RRULE                                                                                                  |
| `getDaysOfYearMatchingByMonthDayRRule           | `$byMonthDays`, `$initialDateTime`                                                            | `protected` | Find all days of a year that match the BYMONTHDAY stanza of an RRULE                                                                                             |
| `getDaysOfYearMatchingByWeekNoRRule`            | `$byWeekNums`, `$initialDateTime`                                                             | `protected` | Find all days of a year that match the BYWEEKNO stanza of an RRULE                                                                                               |
| `getDaysOfYearMatchingByYearDayRRule`           | `$byYearDays`, `$initialDateTime`                                                             | `protected` | Find all days of a year that match the BYYEARDAY stanza of an RRULE                                                                                              |
| `hasEvents`                                     | -                                                                                             | `public`    | Returns a boolean value whether the current calendar has events or not                                                                                           |
| `iCalDateToDateTime`                            | `$icalDate`                                                                                   | `public`    | Returns a `DateTime` object from an iCal date time format                                                                                                        |
| `iCalDateToUnixTimestamp`                       | `$icalDate`                                                                                   | `public`    | Returns a Unix timestamp from an iCal date time format                                                                                                           |
| `iCalDateWithTimeZone`                          | `$event`, `$key`, `$format = DATE_TIME_FORMAT`                                                | `public`    | Returns a date adapted to the calendar time zone depending on the event `TZID`                                                                                   |
| `doesEventStartOutsideWindow`                   | `$event`                                                                                      | `protected` | Determines whether the event start date is outside `$windowMinTimestamp` / `$windowMaxTimestamp`                                                                 |
| `isFileOrUrl`                                   | `$filename`                                                                                   | `protected` | Checks if a filename exists as a file or URL                                                                                                                     |
| `isOutOfRange`                                  | `$calendarDate`, `$minTimestamp`, `$maxTimestamp`                                             | `protected` | Determines whether a valid iCalendar date is within a given range                                                                                                |
| `isValidCldrTimeZoneId`                         | `$timeZone`                                                                                   | `protected` | Checks if a time zone is a valid CLDR time zone                                                                                                                  |
| `isValidDate`                                   | `$value`                                                                                      | `public`    | Checks if a date string is a valid date                                                                                                                          |
| `isValidIanaTimeZoneId`                         | `$timeZone`                                                                                   | `protected` | Checks if a time zone is a valid IANA time zone                                                                                                                  |
| `isValidWindowsTimeZoneId`                      | `$timeZone`                                                                                   | `protected` | Checks if a time zone is a recognised Windows (non-CLDR) time zone                                                                                               |
| `isValidTimeZoneId`                             | `$timeZone`                                                                                   | `protected` | Checks if a time zone is valid (IANA, CLDR, or Windows)                                                                                                          |
| `keyValueFromString`                            | `$text`                                                                                       | `protected` | Gets the key value pair from an iCal string                                                                                                                      |
| `mb_chr`                                        | `$code`                                                                                       | `protected` | Provides a polyfill for PHP 7.2's `mb_chr()`, which is a multibyte safe version of `chr()`                                                                       |
| `mb_str_replace`                                | `$search`, `$replace`, `$subject`, `$count = 0`                                               | `protected` | Replaces all occurrences of a search string with a given replacement string                                                                                      |
| `escapeParamText`                               | `$candidateText`                                                                              | `protected` | Places double-quotes around texts that have characters not permitted in parameter-texts, but are permitted in quoted-texts.                                      |
| `parseDuration`                                 | `$date`, `$duration`, `$format = 'U'`                                                         | `protected` | Parses a duration and applies it to a date                                                                                                                       |
| `parseExdates`                                  | `$event`                                                                                      | `public`    | Parses a list of excluded dates to be applied to an Event                                                                                                        |
| `processDateConversions`                        | -                                                                                             | `protected` | Processes date conversions using the time zone                                                                                                                   |
| `processEvents`                                 | -                                                                                             | `protected` | Performs admin tasks on all events as read from the iCal file                                                                                                    |
| `processRecurrences`                            | -                                                                                             | `protected` | Processes recurrence rules                                                                                                                                       |
| `reduceEventsToMinMaxRange`                     |                                                                                               | `protected` | Reduces the number of events to the defined minimum and maximum range                                                                                            |
| `removeLastEventIfOutsideWindowAndNonRecurring` |                                                                                               | `protected` | Removes the last event (i.e. most recently parsed) if its start date is outside the window spanned by `$windowMinTimestamp` / `$windowMaxTimestamp`              |
| `removeUnprintableChars`                        | `$data`                                                                                       | `protected` | Removes unprintable ASCII and UTF-8 characters                                                                                                                   |
| `resolveIndicesOfRange`                         | `$indexes`, `$limit`                                                                          | `protected` | Resolves values from indices of the range 1 -> `$limit`                                                                                                          |
| `sortEventsWithOrder`                           | `$events`, `$sortOrder = SORT_ASC`                                                            | `public`    | Sorts events based on a given sort order                                                                                                                         |
| `timeZoneStringToDateTimeZone`                  | `$timeZoneString`                                                                             | `public`    | Returns a `DateTimeZone` object based on a string containing a time zone name.                                                                                   |
| `unfold`                                        | `$lines`                                                                                      | `protected` | Unfolds an iCal file in preparation for parsing                                                                                                                  |

#### Constants

| Name                      | Description                                   |
|---------------------------|-----------------------------------------------|
| `DATE_TIME_FORMAT_PRETTY` | Default pretty date time format to use        |
| `DATE_TIME_FORMAT`        | Default date time format to use               |
| `ICAL_DATE_TIME_TEMPLATE` | String template to generate an iCal date time |
| `ISO_8601_WEEK_START`     | First day of the week, as defined by ISO-8601 |
| `RECURRENCE_EVENT`        | Used to isolate generated recurrence events   |
| `SECONDS_IN_A_WEEK`       | The number of seconds in a week               |
| `TIME_FORMAT`             | Default time format to use                    |
| `TIME_ZONE_UTC`           | UTC time zone string                          |
| `UNIX_FORMAT`             | Unix timestamp date format                    |
| `UNIX_MIN_YEAR`           | The year Unix time began                      |

---

### `Event` API (extends `ICal` API)

#### Methods

| Method        | Parameter(s)                                | Visibility  | Description                                                         |
|---------------|---------------------------------------------|-------------|---------------------------------------------------------------------|
| `__construct` | `$data = array()`                           | `public`    | Creates the Event object                                            |
| `prepareData` | `$value`                                    | `protected` | Prepares the data for output                                        |
| `printData`   | `$html = HTML_TEMPLATE`                     | `public`    | Returns Event data excluding anything blank within an HTML template |
| `snakeCase`   | `$input`, `$glue = '_'`, `$separator = '-'` | `protected` | Converts the given input to snake_case                              |

#### Constants

| Name            | Description                                         |
|-----------------|-----------------------------------------------------|
| `HTML_TEMPLATE` | String template to use when pretty printing content |

---

## Credits
 - [Jonathan Goode](https://github.com/u01jmg3) (programming, bug fixing, codebase enhancement, coding standard adoption)
 - [s0600204](https://github.com/s0600204) (major enhancements to RRULE support, many bug fixes and other contributions)

---

## Tools for Testing

 - [iCal Validator](https://icalendar.org/validator.html)
 - [Recurrence Rule Tester](https://jakubroztocil.github.io/rrule/)
 - [Unix Timestamp Converter](https://www.unixtimestamp.com)
