# PHP ICS Parser

[![Latest Stable Version](https://poser.pugx.org/johngrogg/ics-parser/v/stable.png)](https://packagist.org/packages/johngrogg/ics-parser)
[![Total Downloads](https://poser.pugx.org/johngrogg/ics-parser/downloads.png)](https://packagist.org/packages/johngrogg/ics-parser)

--

## Installation

### Requirements
  - PHP 5 >= 5.3.0

### [Composer](http://getcomposer.org)

```bash
$ curl -s https://getcomposer.org/installer | php
```

- `composer.json`

```yaml
{
    "require": {
        "johngrogg/ics-parser": "dev-master"
    }
}
```

--

## API

|Function                 |Parameter(s)                              |Description                                                                                                                  |
|-------------------------|------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------|
|`initLines`              |`$lines`                                  |Initializes lines from file                                                                                                  |
|`initString`             |`$contents`                               |Initializes lines from a string                                                                                              |
|`initURL`                |`$url`                                    |Initializes lines from a URL                                                                                                 |
|`calendarDescription`    |-                                         |Returns the calendar description                                                                                             |
|`calendarName`           |-                                         |Returns the calendar name                                                                                                    |
|`calendarTimeZone`       |-                                         |Returns the calendar timezone                                                                                                |
|`events`                 |-                                         |Returns an array of EventObjects. Every event is a class with the event details being properties within it.                  |
|`eventsFromRange`        |`$rangeStart = false`, `$rangeEnd = false`|Returns a sorted array of the events in a given range, or an empty array if no events exist in the range.                    |
|`freeBusyEvents`         |-                                         |Returns an array of arrays with all free/busy events. Every event is an associative array and each property is an element it.|
|`hasEvents`              |-                                         |Returns a boolean value whether the current calendar has events or not                                                       |
|`iCalDateToUnixTimestamp`|`$icalDate`                               |Return Unix timestamp from iCal date time format                                                                             |
|`iCalDateWithTimeZone`   |`$event`, `$key`                          |Return a date adapted to the calendar timezone depending on the event TZID                                                   |
|`processDateConversions` |-                                         |Add fields `DTSTART_tz` and `DTEND_tz` to each event                                                                         |
|`processEvents`          |-                                         |Performs some admin tasks on all events as taken straight from the ics file.                                                 |
|`processRecurrences`     |-                                         |Processes recurrence rules                                                                                                   |
|`sortEventsWithOrder`    |`$events`, `$sortOrder = SORT_ASC`        |Sort events based on a given sort order                                                                                      |

--

## Credits
  - Martin Thoma (programming, bug fixing, project management)
  - Frank Gregor (programming, feedback, testing)
  - John Grogg (programming, addition of event recurrence handling)
  - [Jonathan Goode](https://github.com/u01jmg3) (programming, bug fixing, enhancement, coding standard)

--

## License

This ics-parser is under [MIT License](http://opensource.org/licenses/MIT). You may use it for your own sites for free, but I would like to get a notice when you use it (info@martin-thoma.de). If you use it for another software project, please state the information / links to this project in the files.

It is hosted at [https://github.com/MartinThoma/ics-parser/](https://github.com/MartinThoma/ics-parser/) and the PEAR coding standard is used.

It was modified by John Grogg to properly handle recurring events (specifically with regards to Microsoft Exchange).

---

## Tools for Testing

- https://jkbrzt.github.io/rrule/
- http://www.unixtimestamp.com/