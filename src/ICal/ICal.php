<?php
/**
 * This PHP class should only read an iCal file (*.ics), parse it and return an
 * array with its content.
 *
 * PHP Version >= 5.3.0
 *
 * @category Parser
 * @package  ics-parser
 * @author   Martin Thoma <info@martin-thoma.de>, John Grogg <john.grogg@gmail.com>, Jonathan Goode <https://github.com/u01jmg3>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     https://github.com/MartinThoma/ics-parser/
 * @example  $ical = new ical('MyCal.ics'); print_r($ical->events());
 * @version  2.0.1
 */

namespace ICal;

use ICal\EventObject;

class ICal
{
    /**
     * How many ToDos are in this iCal?
     * @var integer
     */
    public $todoCount = 0;

    /**
     * How many events are in this iCal?
     * @var integer
     */
    public $eventCount = 0;

    /**
     * How many freebusy are in this iCal?
     * @var integer
     */
    public $freebusyCount = 0;

    /**
     * The parsed calendar
     * @var array
     */
    public $cal;

    /**
     * Event recurrence instances that have been altered
     * @var array
     */
    protected $alteredRecurrenceInstances = array();

    /**
     * Which keyword has been added to cal at last?
     * @var string
     */
    private $lastKeyword;

    /**
     * The value in years to use for indefinite, recurring events
     * @var integer
     */
    public $defaultSpan = 2;
    public $defaultWeekStart = 'SU';

    const UNIX_MIN_YEAR = 1970;

    const DATE_FORMAT = 'Ymd';
    const TIME_FORMAT = 'His';
    const DATE_TIME_FORMAT = 'Ymd\THis';

    protected $dayOrdinals = array(
        1 => 'first',
        2 => 'second',
        3 => 'third',
        4 => 'fourth',
        5 => 'fifth',
        6 => 'last',
    );

    protected $weekdays = array(
        'SU' => 'sunday',
        'MO' => 'monday',
        'TU' => 'tuesday',
        'WE' => 'wednesday',
        'TH' => 'thursday',
        'FR' => 'friday',
        'SA' => 'saturday',
    );

    protected $monthNames = array(
         1 => 'January',
         2 => 'February',
         3 => 'March',
         4 => 'April',
         5 => 'May',
         6 => 'June',
         7 => 'July',
         8 => 'August',
         9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    );

    protected $frequencyConversion = array(
        'DAILY'   => 'day',
        'WEEKLY'  => 'week',
        'MONTHLY' => 'month',
        'YEARLY'  => 'year',
    );

    /**
     * Creates the iCal Object
     *
     * @param mixed $filename  The path to the iCal-file or an array of lines from an iCal file
     * @param mixed $weekStart The default first day of the week (SU or MO, etc.)
     *
     * @return void or false if no filename is provided
     */
    public function __construct($filename = false, $weekStart = false)
    {
        if (!$filename) {
            return false;
        }

        if (is_array($filename)) {
            $lines = $filename;
        } else {
            $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }

        if ($weekStart) {
            $this->defaultWeekStart = $weekStart;
        }

        $this->initLines($lines);
    }

    /**
     * Initialises lines from a URL
     *
     * @param string $url The url of the ical file to download and initialise
     *
     * @return ICal
     */
    public function initURL($url)
    {
        $contents = file_get_contents($url);

        $lines = explode("\n", $contents);

        return $this->initLines($lines);
    }

    /**
     * Initialises lines from a string
     *
     * @param string $contents The contents of the ical file to initialise
     *
     * @return ICal
     */
    public function initString($contents)
    {
        $lines = explode("\n", $contents);

        return $this->initLines($lines);
    }

    /**
     * Initialises lines from file
     *
     * @param array $lines The lines to initialise
     *
     * @return ICal
     */
    public function initLines($lines)
    {
        if (stristr($lines[0], 'BEGIN:VCALENDAR') !== false) {
            $component = '';
            foreach ($lines as $line) {
                $line = rtrim($line); // Trim trailing whitespace
                $add  = $this->keyValueFromString($line);

                if ($add === false) {
                    $this->addCalendarComponentWithKeyAndValue($component, false, $line);
                    continue;
                }

                $keyword = $add[0];
                $values  = $add[1]; // Could be an array containing multiple values

                if (!is_array($values)) {
                    if (!empty($values)) {
                        $values = array($values); // Make an array as not already
                        $blank_array = array(); // Empty placeholder array
                        array_push($values, $blank_array);
                    } else {
                        $values = array(); // Use blank array to ignore this line
                    }
                } else if (empty($values[0])) {
                    $values = array(); // Use blank array to ignore this line
                }

                $values = array_reverse($values); // Reverse so that our array of properties is processed first

                foreach ($values as $value) {
                    switch ($line) {
                        // http://www.kanzaki.com/docs/ical/vtodo.html
                        case 'BEGIN:VTODO':
                            $this->todoCount++;
                            $component = 'VTODO';
                        break;

                        // http://www.kanzaki.com/docs/ical/vevent.html
                        case 'BEGIN:VEVENT':
                            if (!is_array($value)) {
                                $this->eventCount++;
                            }
                            $component = 'VEVENT';
                        break;

                        // http://www.kanzaki.com/docs/ical/vfreebusy.html
                        case 'BEGIN:VFREEBUSY':
                            $this->freebusyCount++;
                            $component = 'VFREEBUSY';
                        break;

                        // All other special strings
                        case 'BEGIN:VCALENDAR':
                        case 'BEGIN:DAYLIGHT':
                            // http://www.kanzaki.com/docs/ical/vtimezone.html
                        case 'BEGIN:VTIMEZONE':
                        case 'BEGIN:STANDARD':
                        case 'BEGIN:VALARM':
                            $component = $value;
                        break;

                        case 'END:VALARM':
                        case 'END:VTODO': // End special text - goto VCALENDAR key
                        case 'END:VEVENT':
                        case 'END:VFREEBUSY':
                        case 'END:VCALENDAR':
                        case 'END:DAYLIGHT':
                        case 'END:VTIMEZONE':
                        case 'END:STANDARD':
                            $component = 'VCALENDAR';
                        break;

                        default:
                            $this->addCalendarComponentWithKeyAndValue($component, $keyword, $value);
                        break;
                    }
                }
            }

            $this->processEvents();
            $this->processRecurrences();
            $this->processDateConversions();
        }
    }

    /**
     * Add to $this->ical array one value and key.
     *
     * @param string         $component This could be VTODO, VEVENT, VCALENDAR, ...
     * @param string|boolean $keyword   The keyword, for example DTSTART
     * @param string         $value     The value, for example 20110105T090000Z
     *
     * @return void
     */
    protected function addCalendarComponentWithKeyAndValue($component, $keyword, $value)
    {
        if ($keyword == false) {
            $keyword = $this->lastKeyword;
        }

        switch ($component) {
            case 'VTODO':
                $this->cal[$component][$this->todoCount - 1][$keyword] = $value;
            break;

            case 'VEVENT':
                if (!isset($this->cal[$component][$this->eventCount - 1][$keyword . '_array'])) {
                    $this->cal[$component][$this->eventCount - 1][$keyword . '_array'] = array();
                }

                if (is_array($value)) {
                    // Add array of properties to the end
                    array_push($this->cal[$component][$this->eventCount - 1][$keyword . '_array'], $value);
                } else {
                    if (!isset($this->cal[$component][$this->eventCount - 1][$keyword])) {
                        $this->cal[$component][$this->eventCount - 1][$keyword] = $value;
                    }

                    if ($keyword === 'EXDATE') {
                        if (trim($value) === $value) {
                            $this->cal[$component][$this->eventCount - 1][$keyword . '_array'][] = explode(',', $value);
                        } else {
                            $value = explode(',', implode(',', $this->cal[$component][$this->eventCount - 1][$keyword . '_array'][1]) . trim($value));
                            $this->cal[$component][$this->eventCount - 1][$keyword . '_array'][1] = $value;
                        }
                    } else {
                        $this->cal[$component][$this->eventCount - 1][$keyword . '_array'][] = $value;

                        if ($keyword === 'DURATION') {
                            $duration = new \DateInterval($value);
                            array_push($this->cal[$component][$this->eventCount - 1][$keyword . '_array'], $duration);
                        }
                    }

                    // Glue back together for multi-line content
                    if ($this->cal[$component][$this->eventCount - 1][$keyword] != $value) {
                        $ord = (isset($value[0])) ? ord($value[0]) : null; // First char

                        if (in_array($ord, array(9, 32))) { // Is space or tab?
                            $value = substr($value, 1); // Only trim the first character
                        }

                        // Account for multiple definitions of current keyword (e.g. ATTENDEE)
                        if (is_array($this->cal[$component][$this->eventCount - 1][$keyword . '_array'][1])) {
                            if ($keyword === 'EXDATE') {
                                // This will give out a comma separated EXDATE string as per RFC2445
                                // Example: EXDATE:19960402T010000Z,19960403T010000Z,19960404T010000Z
                                // Usage: $event['EXDATE'] will print out 19960402T010000Z,19960403T010000Z,19960404T010000Z
                                $value = (is_array($value)) ? implode(',', $value) : $value;
                                $this->cal[$component][$this->eventCount - 1][$keyword] = $value;
                            } else {
                                // Concat value *with separator* as content spans multiple lines
                                $this->cal[$component][$this->eventCount - 1][$keyword] .= ';' . $value;
                            }
                        } else {
                            // Concat value as content spans multiple lines
                            $this->cal[$component][$this->eventCount - 1][$keyword] .= $value;
                        }
                    }
                }
            break;

            case 'VFREEBUSY':
                $this->cal[$component][$this->freebusyCount - 1][$keyword] = $value;
            break;

            default:
                $this->cal[$component][$keyword] = $value;
            break;
        }
        $this->lastKeyword = $keyword;
    }

    /**
     * Get a key-value pair of a string.
     *
     * @param string $text which is like "VCALENDAR:Begin" or "LOCATION:"
     *
     * @return array
     */
    protected function keyValueFromString($text)
    {
        // Match colon separator outside of quoted substrings
        // Fallback to nearest semicolon outside of quoted substrings, if colon cannot be found
        // Do not try and match within the value paired with the keyword
        preg_match('/(.*?)(?::(?=(?:[^"]*"[^"]*")*[^"]*$)|;(?=[^:]*$))([\w\W]*)/', htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8'), $matches);

        if (count($matches) == 0) {
            return false;
        }

        if (preg_match('/^([A-Z-]+)([;][\w\W]*)?$/', $matches[1])) {
            $matches = array_splice($matches, 1, 2); // Remove first match and re-align ordering

            // Process properties
            if (preg_match('/([A-Z-]+)[;]([\w\W]*)/', $matches[0], $properties)) {
                // Remove first match
                array_shift($properties);
                // Fix to ignore everything in keyword after a ; (e.g. Language, TZID, etc.)
                $matches[0] = $properties[0];
                array_shift($properties); // Repeat removing first match

                $formatted = array();
                foreach ($properties as $property) {
                    // Match semicolon separator outside of quoted substrings
                    preg_match_all('~[^\r\n";]+(?:"[^"\\\]*(?:\\\.[^"\\\]*)*"[^\r\n";]*)*~', $property, $attributes);
                    // Remove multi-dimensional array and use the first key
                    $attributes = (sizeof($attributes) == 0) ? array($property) : reset($attributes);

                    if (is_array($attributes)) {
                        foreach ($attributes as $attribute) {
                            // Match equals sign separator outside of quoted substrings
                            preg_match_all(
                                '~[^\r\n"=]+(?:"[^"\\\]*(?:\\\.[^"\\\]*)*"[^\r\n"=]*)*~',
                                $attribute,
                                $values
                            );
                            // Remove multi-dimensional array and use the first key
                            $value = (sizeof($values) == 0) ? null : reset($values);

                            if (is_array($value) && isset($value[1])) {
                                // Remove double quotes from beginning and end only
                                $formatted[$value[0]] = trim($value[1], '"');
                            }
                        }
                    }
                }

                // Assign the keyword property information
                $properties[0] = $formatted;

                // Add match to beginning of array
                array_unshift($properties, $matches[1]);
                $matches[1] = $properties;
            }

            return $matches;
        } else {
            return false; // Ignore this match
        }
    }

    /**
     * Return Unix timestamp from iCal date time format
     *
     * @param string $icalDate A Date in the format YYYYMMDD[T]HHMMSS[Z] or
     *                           YYYYMMDD[T]HHMMSS or
     *                           TZID=Timezone:YYYYMMDD[T]HHMMSS
     *
     * @return integer
     */
    public function iCalDateToUnixTimestamp($icalDate)
    {
        /**
         * iCal times may be in 3 formats, ref http://www.kanzaki.com/docs/ical/dateTime.html
         * UTC:      Has a trailing 'Z'
         * Floating: No timezone reference specified, no trailing 'Z', use local time
         * TZID:     Set timezone as specified
         * Use DateTime class objects to get around limitations with mktime and gmmktime. Must have a local timezone set
         * to process floating times.
         */

        $pattern  = '/\AT?Z?I?D?=?(.*):?'; // 1: TimeZone
        $pattern .= '([0-9]{4})';          // 2: YYYY
        $pattern .= '([0-9]{2})';          // 3: MM
        $pattern .= '([0-9]{2})';          // 4: DD
        $pattern .= 'T?';                  //    Time delimiter
        $pattern .= '([0-9]{0,2})';        // 5: HH
        $pattern .= '([0-9]{0,2})';        // 6: MM
        $pattern .= '([0-9]{0,2})';        // 7: SS
        $pattern .= '(Z?)/';               // 8: UTC flag
        preg_match($pattern, $icalDate, $date);
        $timezone = str_replace(':', '', $date[1]);

        // Unix timestamp can't represent dates before 1970
        if ($date[2] <= self::UNIX_MIN_YEAR) {
            return false;
        }
        // Unix timestamps after 03:14:07 UTC 2038-01-19 might cause an overflow
        // if 32 bit integers are used.
        if ($date[8] == 'Z') {
            $convDate = new \DateTime('now', new \DateTimeZone('UTC'));
        } else if (!$timezone) {
            $convDate = new \DateTime('now', new \DateTimeZone($this->calendarTimeZone()));
        } else if ($this->isValidTimeZoneId($timezone)) {
            $convDate = new \DateTime('now', new \DateTimeZone($timezone));
        } else {
            $convDate = new \DateTime('now', new \DateTimeZone(date_default_timezone_get()));
        }

        $convDate->setDate((int) $date[2], (int) $date[3], (int) $date[4]);
        $convDate->setTime((int) $date[5], (int) $date[6], (int) $date[7]);
        $timestamp = $convDate->getTimestamp();

        return $timestamp;
    }

    /**
     * Return a date adapted to the calendar timezone depending on the event TZID
     *
     * @param array  $event an event
     * @param string $key   an event parameter (DTSTART or DTEND)
     *
     * @return string Ymd\THis date
     */
    public function iCalDateWithTimeZone($event, $key)
    {
        $defaultTimeZone = $this->calendarTimeZone();
        if (!$defaultTimeZone) {
            return false;
        }

        if (!isset($event[$key . '_array']) || !isset($event[$key])) {
            return false;
        }

        $date_array = $event[$key . '_array'];
        $date       = $event[$key];

        if (isset($date_array[0]['TZID']) && preg_match('/[a-z]*\/[a-z_]*/i', $date_array[0]['TZID'])) {
            $timeZone = $date_array[0]['TZID'];
        }

        // Check if the defined timezone is valid
        if (!isset($timeZone) || !in_array($timeZone, timezone_identifiers_list())) {
            $timeZone = $defaultTimeZone;
        }

        $dateTime = new \DateTime($event[$key]);

        if (substr($date, -1) === 'Z') {
            $date = substr($date, 0, -1);
            $tz = new \DateTimeZone($defaultTimeZone);
            $offset = timezone_offset_get($tz, $dateTime);
        } else {
            $tz = new \DateTimeZone($defaultTimeZone);
            $offset1 = timezone_offset_get($tz, $dateTime);
            $tz = new \DateTimeZone($timeZone);
            $offset2 = timezone_offset_get($tz, $dateTime);
            $offset = $offset1 - $offset2;
        }

        if ($offset >= 0) {
            $offset = '+' . $offset;
        }

        $time = strtotime($date . " $offset seconds");

        return date('Ymd\THis', $time);
    }

    /**
     * Performs some admin tasks on all events as taken straight from the ics file.
     * Adds a unix timestamp to all `DT{START|END|RECURRENCE-ID}_array` arrays
     * Makes a note of modified recurrence-instances
     *
     * @return void or false if no Events exist
     */
    public function processEvents()
    {
        $events = (isset($this->cal['VEVENT'])) ? $this->cal['VEVENT'] : array();

        if (empty($events)) {
            return false;
        }

        foreach ($events as $key => $anEvent) {
            foreach (array('DTSTART', 'DTEND', 'RECURRENCE-ID') as $type) {
                if (isset($anEvent[$type])) {
                    $date = $anEvent[$type . '_array'][1];
                    if (isset($anEvent[$type . '_array'][0]['TZID'])) {
                        $date = 'TZID=' . $anEvent[$type . '_array'][0]['TZID'] . ':' . $date;
                    }
                    $anEvent[$type . '_array'][2] = $this->iCalDateToUnixTimestamp($date);
                }
            }

            if (isset($anEvent['RECURRENCE-ID'])) {
                $uid = $anEvent['UID'];
                if (!isset($this->alteredRecurrenceInstances[$uid])) {
                    $this->alteredRecurrenceInstances[$uid] = array();
                }
                $this->alteredRecurrenceInstances[$uid][] = $anEvent['RECURRENCE-ID_array'][2];
            }

            $events[$key] = $anEvent;
        }

        $this->cal['VEVENT'] = $events;
    }

    /**
     * Processes recurrence rules
     *
     * @return void or false if no Events exist
     */
    public function processRecurrences()
    {
        $events = (isset($this->cal['VEVENT'])) ? $this->cal['VEVENT'] : array();

        if (empty($events)) {
            return false;
        }

        foreach ($events as $anEvent) {
            if (isset($anEvent['RRULE']) && $anEvent['RRULE'] != '') {
                if (isset($anEvent['DTSTART_array'][0]['TZID']) && $this->isValidTimeZoneId($anEvent['DTSTART_array'][0]['TZID'])) {
                    $initialStartTimeZone = $anEvent['DTSTART_array'][0]['TZID'];
                }

                $initialStart       = new \DateTime($anEvent['DTSTART_array'][1], isset($initialStartTimeZone) ? new \DateTimeZone($initialStartTimeZone) : null);
                $initialStartOffset = $initialStart->getOffset();

                if (isset($anEvent['DTEND'])) {
                    if (isset($anEvent['DTEND_array'][0]['TZID']) && $this->isValidTimeZoneId($anEvent['DTSTART_array'][0]['TZID'])) {
                        $initialEndTimeZone = $anEvent['DTSTART_array'][0]['TZID'];
                    }

                    $initialEnd       = new \DateTime($anEvent['DTEND_array'][1], isset($initialEndTimeZone) ? new \DateTimeZone($initialEndTimeZone) : null);
                    $initialEndOffset = $initialEnd->getOffset();
                }

                // Recurring event, parse RRULE and add appropriate duplicate events
                $rrules = array();
                $rruleStrings = explode(';', $anEvent['RRULE']);
                foreach ($rruleStrings as $s) {
                    list($k, $v) = explode('=', $s);
                    $rrules[$k] = $v;
                }
                // Get frequency
                $frequency = $rrules['FREQ'];
                // Get Start timestamp
                $startTimestamp = $initialStart->getTimeStamp();
                if (isset($anEvent['DTEND'])) {
                    $endTimestamp = $initialEnd->getTimestamp();
                } else if (isset($anEvent['DURATION'])) {
                    $duration = end($anEvent['DURATION_array']);
                    $endTimestamp = date_create($anEvent['DTSTART']);
                    $endTimestamp->modify($duration->y . ' year');
                    $endTimestamp->modify($duration->m . ' month');
                    $endTimestamp->modify($duration->d . ' day');
                    $endTimestamp->modify($duration->h . ' hour');
                    $endTimestamp->modify($duration->i . ' minute');
                    $endTimestamp->modify($duration->s . ' second');
                    $endTimestamp = date_format($endTimestamp, 'U');
                } else {
                    $endTimestamp = $anEvent['DTSTART_array'][2];
                }
                $eventTimestampOffset = $endTimestamp - $startTimestamp;
                // Get Interval
                $interval = (isset($rrules['INTERVAL']) && $rrules['INTERVAL'] != '')
                    ? $rrules['INTERVAL']
                    : 1;

                $dayNumber = null;
                $weekDay = null;

                if (in_array($frequency, array('MONTHLY', 'YEARLY'))
                    && isset($rrules['BYDAY']) && $rrules['BYDAY'] != ''
                ) {
                    // Deal with BYDAY
                    $dayNumber = intval($rrules['BYDAY']);
                    if (empty($dayNumber)) { // Returns 0 when no number defined in BYDAY
                        if (!isset($rrules['BYSETPOS'])) {
                            $dayNumber = 1; // Set first as default
                        } else if (is_numeric($rrules['BYSETPOS'])) {
                            $dayNumber = $rrules['BYSETPOS'];
                        }
                    }
                    $dayNumber = ($dayNumber == -1) ? 6 : $dayNumber; // Override for our custom key (6 => 'last')
                    $weekDay = substr($rrules['BYDAY'], -2);
                }

                $untilDefault = date_create('now');
                $untilDefault->modify($this->defaultSpan . ' year');
                $untilDefault->setTime(23, 59, 59); // End of the day

                if (isset($rrules['UNTIL'])) {
                    // Get Until
                    $until = strtotime($rrules['UNTIL']);
                } else if (isset($rrules['COUNT'])) {
                    $countOrig = (is_numeric($rrules['COUNT']) && $rrules['COUNT'] > 1) ? $rrules['COUNT'] : 0;
                    $count = ($countOrig - 1); // Remove one to exclude the occurrence that initialises the rule
                    $count += ($count > 0) ? $count * ($interval - 1) : 0;
                    $countNb = 1;
                    $offset = "+$count " . $this->frequencyConversion[$frequency];
                    $until = strtotime($offset, $startTimestamp);

                    if (in_array($frequency, array('MONTHLY', 'YEARLY'))
                        && isset($rrules['BYDAY']) && $rrules['BYDAY'] != ''
                    ) {
                        $dtstart = date_create($anEvent['DTSTART']);
                        for ($i = 1; $i <= $count; $i++) {
                            $dtstartClone = clone $dtstart;
                            $dtstartClone->modify('next ' . $this->frequencyConversion[$frequency]);
                            $offset = "{$this->dayOrdinals[$dayNumber]} {$this->weekdays[$weekDay]} of " . $dtstartClone->format('F Y H:i:01');
                            $dtstart->modify($offset);
                        }

                        /**
                         * Jumping X months forwards doesn't mean
                         * the end date will fall on the same day defined in BYDAY
                         * Use the largest of these to ensure we are going far enough
                         * in the future to capture our final end day
                         */
                        $until = max($until, $dtstart->format('U'));
                    }

                    unset($offset);
                } else {
                    $until = $untilDefault->getTimestamp();
                }

                if (!isset($anEvent['EXDATE_array'])) {
                    $anEvent['EXDATE_array'][1] = array();
                }

                // Decide how often to add events and do so
                switch ($frequency) {
                    case 'DAILY':
                        // Simply add a new event each interval of days until UNTIL is reached
                        $offset = "+$interval day";
                        $recurringTimestamp = strtotime($offset, $startTimestamp);

                        while ($recurringTimestamp <= $until) {
                            $dayRecurringTimestamp = $recurringTimestamp;

                            // Adjust timezone from initial event
                            $recurringTimeZone = \DateTime::createFromFormat('U', $dayRecurringTimestamp);
                            $timezoneOffset = $initialStart->getTimezone()->getOffset($recurringTimeZone);
                            $dayRecurringTimestamp += ($timezoneOffset != $initialStartOffset) ? $initialStartOffset - $timezoneOffset : 0;

                            // Add event
                            $anEvent['DTSTART'] = gmdate(self::DATE_TIME_FORMAT, $dayRecurringTimestamp) . 'Z';
                            $anEvent['DTSTART_array'] = array(array(), $anEvent['DTSTART'], $dayRecurringTimestamp);
                            $anEvent['DTEND_array'] = $anEvent['DTSTART_array'];
                            $anEvent['DTEND_array'][2] += $eventTimestampOffset;
                            $anEvent['DTEND'] = gmdate(
                                self::DATE_TIME_FORMAT,
                                $anEvent['DTEND_array'][2]
                            ) . 'Z';
                            $anEvent['DTEND_array'][1] = $anEvent['DTEND'];

                            $searchDate = $anEvent['DTSTART'];
                            $isExcluded = array_filter($anEvent['EXDATE_array'][1], function ($val) use ($searchDate) {
                                return is_string($val) && strpos($searchDate, $val) === 0;
                            });

                            if (isset($this->alteredRecurrenceInstances[$anEvent['UID']]) && in_array($dayRecurringTimestamp, $this->alteredRecurrenceInstances[$anEvent['UID']])) {
                                $isExcluded = true;
                            }

                            if (!$isExcluded) {
                                $events[] = $anEvent;
                                $this->eventCount++;

                                // If RRULE[COUNT] is reached then break
                                if (isset($rrules['COUNT'])) {
                                    $countNb++;

                                    if ($countNb >= $countOrig) {
                                        break 2;
                                    }
                                }
                            }

                            // Move forwards
                            $recurringTimestamp = strtotime($offset, $recurringTimestamp);
                        }
                    break;

                    case 'WEEKLY':
                        // Create offset
                        $offset = "+$interval week";

                        // Use RRULE['WKST'] setting or a default week start (UK = SU, Europe = MO)
                        $weeks = array(
                            'SA' => array('SA', 'SU', 'MO', 'TU', 'WE', 'TH', 'FR'),
                            'SU' => array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'),
                            'MO' => array('MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'),
                        );

                        $wkst = (isset($rrules['WKST']) && in_array($rrules['WKST'], array('SA', 'SU', 'MO'))) ? $rrules['WKST'] : $this->defaultWeekStart;
                        $aWeek = $weeks[$wkst];
                        $days = array('SA' => 'Saturday', 'SU' => 'Sunday', 'MO' => 'Monday');

                        // Build list of days of week to add events
                        $weekdays = $aWeek;

                        if (isset($rrules['BYDAY']) && $rrules['BYDAY'] != '') {
                            $bydays = explode(',', $rrules['BYDAY']);
                        } else {
                            $findDay = $weekdays[gmdate('w', $startTimestamp)];
                            $bydays = array($findDay);
                        }

                        // Get timestamp of first day of start week
                        $weekRecurringTimestamp = (gmdate('w', $startTimestamp) == 0)
                            ? $startTimestamp
                            : strtotime("last {$days[$wkst]} " . gmdate('H:i:s\z', $startTimestamp), $startTimestamp);

                        // Step through weeks
                        while ($weekRecurringTimestamp <= $until) {
                            $dayRecurringTimestamp = $weekRecurringTimestamp;

                            // Adjust timezone from initial event
                            $dayRecurringTimeZone = \DateTime::createFromFormat('U', $dayRecurringTimestamp, new \DateTimeZone('UTC'));
                            $timezoneOffset = $initialStart->getTimezone()->getOffset($dayRecurringTimeZone);
                            $dayRecurringTimestamp += ($timezoneOffset != $initialStartOffset) ? $initialStartOffset - $timezoneOffset : 0;

                            foreach ($weekdays as $day) {
                                // Check if day should be added

                                if (in_array($day, $bydays) && $dayRecurringTimestamp > $startTimestamp
                                    && $dayRecurringTimestamp <= $until
                                ) {
                                    // Add event
                                    $anEvent['DTSTART'] = gmdate(self::DATE_TIME_FORMAT, $dayRecurringTimestamp) . 'Z';
                                    $anEvent['DTSTART_array'] = array(array(), $anEvent['DTSTART'], $dayRecurringTimestamp);
                                    $anEvent['DTEND_array'] = $anEvent['DTSTART_array'];
                                    $anEvent['DTEND_array'][2] += $eventTimestampOffset;
                                    $anEvent['DTEND'] = gmdate(
                                        self::DATE_TIME_FORMAT,
                                        $anEvent['DTEND_array'][2]
                                    ) . 'Z';
                                    $anEvent['DTEND_array'][1] = $anEvent['DTEND'];

                                    $searchDate = $anEvent['DTSTART'];
                                    $isExcluded = array_filter($anEvent['EXDATE_array'][1], function ($val) use ($searchDate) {
                                        return is_string($val) && strpos($searchDate, $val) === 0;
                                    });

                                    if (isset($this->alteredRecurrenceInstances[$anEvent['UID']]) && in_array($dayRecurringTimestamp, $this->alteredRecurrenceInstances[$anEvent['UID']])) {
                                        $isExcluded = true;
                                    }

                                    if (!$isExcluded) {
                                        $events[] = $anEvent;
                                        $this->eventCount++;

                                        // If RRULE[COUNT] is reached then break
                                        if (isset($rrules['COUNT'])) {
                                            $countNb++;

                                            if ($countNb >= $countOrig) {
                                                break 2;
                                            }
                                        }
                                    }
                                }

                                // Move forwards a day
                                $dayRecurringTimestamp = strtotime('+1 day', $dayRecurringTimestamp);
                            }

                            // Move forwards $interval weeks
                            $weekRecurringTimestamp = strtotime($offset, $weekRecurringTimestamp);
                        }
                    break;

                    case 'MONTHLY':
                        // Create offset
                        $offset = "+$interval month";
                        $recurringTimestamp = strtotime($offset, $startTimestamp);

                        if (isset($rrules['BYMONTHDAY']) && $rrules['BYMONTHDAY'] != '') {
                            // Deal with BYMONTHDAY
                            $monthdays = explode(',', $rrules['BYMONTHDAY']);

                            while ($recurringTimestamp <= $until) {
                                foreach ($monthdays as $key => $monthday) {
                                    if ($key === 0) {
                                        // Ensure original event conforms to monthday rule
                                        $events[0]['DTSTART'] = gmdate(
                                            'Ym' . sprintf('%02d', $monthday) . '\T' . self::TIME_FORMAT,
                                            strtotime($events[0]['DTSTART'])
                                        ) . 'Z';

                                        $events[0]['DTEND'] = gmdate(
                                            'Ym' . sprintf('%02d', $monthday) . '\T' . self::TIME_FORMAT,
                                            strtotime($events[0]['DTEND'])
                                        ) . 'Z';

                                        $events[0]['DTSTART_array'][1] = $events[0]['DTSTART'];
                                        $events[0]['DTSTART_array'][2] = $this->iCalDateToUnixTimestamp($events[0]['DTSTART']);
                                        $events[0]['DTEND_array'][1]   = $events[0]['DTEND'];
                                        $events[0]['DTEND_array'][2]   = $this->iCalDateToUnixTimestamp($events[0]['DTEND']);

                                        // Ensure recurring timestamp confirms to monthday rule
                                        $monthRecurringTimestamp = $this->iCalDateToUnixTimestamp(
                                            gmdate(
                                                'Ym' . sprintf('%02d', $monthday) . '\T' . self::TIME_FORMAT,
                                                $recurringTimestamp
                                            ) . 'Z'
                                        );
                                    }

                                    // Adjust timezone from initial event
                                    $recurringTimeZone = \DateTime::createFromFormat('U', $monthRecurringTimestamp);
                                    $timezoneOffset = $initialStart->getTimezone()->getOffset($recurringTimeZone);
                                    $monthRecurringTimestamp += ($timezoneOffset != $initialStartOffset) ? $initialStartOffset - $timezoneOffset : 0;

                                    // Add event
                                    $anEvent['DTSTART'] = gmdate(
                                        'Ym' . sprintf('%02d', $monthday) . '\T' . self::TIME_FORMAT,
                                        $monthRecurringTimestamp
                                    ) . 'Z';
                                    $anEvent['DTSTART_array'] = array(array(), $anEvent['DTSTART'], $monthRecurringTimestamp);
                                    $anEvent['DTEND_array'] = $anEvent['DTSTART_array'];
                                    $anEvent['DTEND_array'][2] += $eventTimestampOffset;
                                    $anEvent['DTEND'] = gmdate(
                                        self::DATE_TIME_FORMAT,
                                        $anEvent['DTEND_array'][2]
                                    ) . 'Z';
                                    $anEvent['DTEND_array'][1] = $anEvent['DTEND'];

                                    $searchDate = $anEvent['DTSTART'];
                                    $isExcluded = array_filter($anEvent['EXDATE_array'][1], function ($val) use ($searchDate) {
                                        return is_string($val) && strpos($searchDate, $val) === 0;
                                    });

                                    if (isset($this->alteredRecurrenceInstances[$anEvent['UID']]) && in_array($monthRecurringTimestamp, $this->alteredRecurrenceInstances[$anEvent['UID']])) {
                                        $isExcluded = true;
                                    }

                                    if (!$isExcluded) {
                                        $events[] = $anEvent;
                                        $this->eventCount++;

                                        // If RRULE[COUNT] is reached then break
                                        if (isset($rrules['COUNT'])) {
                                            $countNb++;

                                            if ($countNb >= $countOrig) {
                                                break 2;
                                            }
                                        }
                                    }
                                }

                                // Move forwards
                                $recurringTimestamp = strtotime($offset, $recurringTimestamp);
                            }
                        } else if (isset($rrules['BYDAY']) && $rrules['BYDAY'] != '') {
                            while ($recurringTimestamp <= $until) {
                                $monthRecurringTimestamp = $recurringTimestamp;

                                // Adjust timezone from initial event
                                $recurringTimeZone = \DateTime::createFromFormat('U', $monthRecurringTimestamp);
                                $timezoneOffset = $initialStart->getTimezone()->getOffset($recurringTimeZone);
                                $monthRecurringTimestamp += ($timezoneOffset != $initialStartOffset) ? $initialStartOffset - $timezoneOffset : 0;

                                $eventStartDesc = "{$this->dayOrdinals[$dayNumber]} {$this->weekdays[$weekDay]} of " . gmdate('F Y H:i:s', $monthRecurringTimestamp);
                                $eventStartTimestamp = strtotime($eventStartDesc);

                                // Prevent 5th day of a month from showing up on the next month
                                // If BYDAY and the event falls outside the current month, skip the event

                                $compareCurrentMonth = date('F', $monthRecurringTimestamp);
                                $compareEventMonth   = date('F', $eventStartTimestamp);

                                if ($compareCurrentMonth != $compareEventMonth) {
                                    $monthRecurringTimestamp = strtotime($offset, $monthRecurringTimestamp);
                                    continue;
                                }

                                if ($eventStartTimestamp > $startTimestamp && $eventStartTimestamp < $until) {
                                    $anEvent['DTSTART'] = gmdate(self::DATE_TIME_FORMAT, $eventStartTimestamp) . 'Z';
                                    $anEvent['DTSTART_array'] = array(array(), $anEvent['DTSTART'], $eventStartTimestamp);
                                    $anEvent['DTEND_array'] = $anEvent['DTSTART_array'];
                                    $anEvent['DTEND_array'][2] += $eventTimestampOffset;
                                    $anEvent['DTEND'] = gmdate(
                                        self::DATE_TIME_FORMAT,
                                        $anEvent['DTEND_array'][2]
                                    ) . 'Z';
                                    $anEvent['DTEND_array'][1] = $anEvent['DTEND'];

                                    $searchDate = $anEvent['DTSTART'];
                                    $isExcluded = array_filter($anEvent['EXDATE_array'][1], function ($val) use ($searchDate) {
                                        return is_string($val) && strpos($searchDate, $val) === 0;
                                    });

                                    if (isset($this->alteredRecurrenceInstances[$anEvent['UID']]) && in_array($monthRecurringTimestamp, $this->alteredRecurrenceInstances[$anEvent['UID']])) {
                                        $isExcluded = true;
                                    }

                                    if (!$isExcluded) {
                                        $events[] = $anEvent;
                                        $this->eventCount++;

                                        // If RRULE[COUNT] is reached then break
                                        if (isset($rrules['COUNT'])) {
                                            $countNb++;

                                            if ($countNb >= $countOrig) {
                                                break 2;
                                            }
                                        }
                                    }
                                }

                                // Move forwards
                                $recurringTimestamp = strtotime($offset, $recurringTimestamp);
                            }
                        }
                    break;

                    case 'YEARLY':
                        // Create offset
                        $offset = "+$interval year";
                        $recurringTimestamp = strtotime($offset, $startTimestamp);

                        // Check if BYDAY rule exists
                        if (isset($rrules['BYDAY']) && $rrules['BYDAY'] != '') {
                            while ($recurringTimestamp <= $until) {
                                $yearRecurringTimestamp = $recurringTimestamp;

                                // Adjust timezone from initial event
                                $recurringTimeZone = \DateTime::createFromFormat('U', $yearRecurringTimestamp);
                                $timezoneOffset = $initialStart->getTimezone()->getOffset($recurringTimeZone);
                                $yearRecurringTimestamp += ($timezoneOffset != $initialStartOffset) ? $initialStartOffset - $timezoneOffset : 0;

                                $eventStartDesc = "{$this->dayOrdinals[$dayNumber]} {$this->weekdays[$weekDay]}"
                                    . " of {$this->monthNames[$rrules['BYMONTH']]} "
                                    . gmdate('Y H:i:s', $yearRecurringTimestamp);
                                $eventStartTimestamp = strtotime($eventStartDesc);

                                if ($eventStartTimestamp > $startTimestamp && $eventStartTimestamp < $until) {
                                    $anEvent['DTSTART'] = gmdate(self::DATE_TIME_FORMAT, $eventStartTimestamp) . 'Z';
                                    $anEvent['DTSTART_array'] = array(array(), $anEvent['DTSTART'], $eventStartTimestamp);
                                    $anEvent['DTEND_array'] = $anEvent['DTSTART_array'];
                                    $anEvent['DTEND_array'][2] += $eventTimestampOffset;
                                    $anEvent['DTEND'] = gmdate(
                                        self::DATE_TIME_FORMAT,
                                        $anEvent['DTEND_array'][2]
                                    ) . 'Z';
                                    $anEvent['DTEND_array'][1] = $anEvent['DTEND'];

                                    $searchDate = $anEvent['DTSTART'];
                                    $isExcluded = array_filter($anEvent['EXDATE_array'][1], function ($val) use ($searchDate) {
                                        return is_string($val) && strpos($searchDate, $val) === 0;
                                    });

                                    if (isset($this->alteredRecurrenceInstances[$anEvent['UID']]) && in_array($yearRecurringTimestamp, $this->alteredRecurrenceInstances[$anEvent['UID']])) {
                                        $isExcluded = true;
                                    }

                                    if (!$isExcluded) {
                                        $events[] = $anEvent;
                                        $this->eventCount++;

                                        // If RRULE[COUNT] is reached then break
                                        if (isset($rrules['COUNT'])) {
                                            $countNb++;

                                            if ($countNb >= $countOrig) {
                                                break 2;
                                            }
                                        }
                                    }
                                }

                                // Move forwards
                                $recurringTimestamp = strtotime($offset, $recurringTimestamp);
                            }
                        } else {
                            $day = gmdate('d', $startTimestamp);

                            // Step through years
                            while ($recurringTimestamp <= $until) {
                                $yearRecurringTimestamp = $recurringTimestamp;

                                // Adjust timezone from initial event
                                $recurringTimeZone = \DateTime::createFromFormat('U', $yearRecurringTimestamp);
                                $timezoneOffset = $initialStart->getTimezone()->getOffset($recurringTimeZone);
                                $yearRecurringTimestamp += ($timezoneOffset != $initialStartOffset) ? $initialStartOffset - $timezoneOffset : 0;

                                // Add specific month dates
                                if (isset($rrules['BYMONTH']) && $rrules['BYMONTH'] != '') {
                                    $eventStartDesc = "$day {$this->monthNames[$rrules['BYMONTH']]} " . gmdate('Y H:i:s', $yearRecurringTimestamp);
                                } else {
                                    $eventStartDesc = $day . gmdate('F Y H:i:s', $yearRecurringTimestamp);
                                }

                                $eventStartTimestamp = strtotime($eventStartDesc);

                                if ($eventStartTimestamp > $startTimestamp && $eventStartTimestamp < $until) {
                                    $anEvent['DTSTART'] = gmdate(self::DATE_TIME_FORMAT, $eventStartTimestamp) . 'Z';
                                    $anEvent['DTSTART_array'] = array(array(), $anEvent['DTSTART'], $eventStartTimestamp);
                                    $anEvent['DTEND_array'] = $anEvent['DTSTART_array'];
                                    $anEvent['DTEND_array'][2] += $eventTimestampOffset;
                                    $anEvent['DTEND'] = gmdate(
                                        self::DATE_TIME_FORMAT,
                                        $anEvent['DTEND_array'][2]
                                    ) . 'Z';
                                    $anEvent['DTEND_array'][1] = $anEvent['DTEND'];

                                    $searchDate = $anEvent['DTSTART'];
                                    $isExcluded = array_filter($anEvent['EXDATE_array'][1], function ($val) use ($searchDate) {
                                        return is_string($val) && strpos($searchDate, $val) === 0;
                                    });

                                    if (isset($this->alteredRecurrenceInstances[$anEvent['UID']]) && in_array($yearRecurringTimestamp, $this->alteredRecurrenceInstances[$anEvent['UID']])) {
                                        $isExcluded = true;
                                    }

                                    if (!$isExcluded) {
                                        $events[] = $anEvent;
                                        $this->eventCount++;

                                        // If RRULE[COUNT] is reached then break
                                        if (isset($rrules['COUNT'])) {
                                            $countNb++;

                                            if ($countNb >= $countOrig) {
                                                break 2;
                                            }
                                        }
                                    }
                                }

                                // Move forwards
                                $recurringTimestamp = strtotime($offset, $recurringTimestamp);
                            }
                        }
                    break;

                    $events = (isset($countOrig) && sizeof($events) > $countOrig) ? array_slice($events, 0, $countOrig) : $events; // Ensure we abide by COUNT if defined
                }
            }
        }

        $this->cal['VEVENT'] = $events;
    }

    /**
     * Processes date conversions using the timezone
     *
     * Add fields DTSTART_tz and DTEND_tz to each event
     * These fields contain dates adapted to the calendar
     * timezone depending on the event TZID
     *
     * @return void or false if no Events exist
     */
    public function processDateConversions()
    {
        $events = (isset($this->cal['VEVENT'])) ? $this->cal['VEVENT'] : array();

        if (empty($events)) {
            return false;
        }

        foreach ($events as $key => $anEvent) {
            $events[$key]['DTSTART_tz'] = $this->iCalDateWithTimeZone($anEvent, 'DTSTART');

            if ($this->iCalDateWithTimeZone($anEvent, 'DTEND')) {
                $events[$key]['DTEND_tz'] = $this->iCalDateWithTimeZone($anEvent, 'DTEND');
            }
        }

        $this->cal['VEVENT'] = $events;
    }

    /**
     * Returns an array of EventObjects. Every event is a class
     * with the event details being properties within it.
     *
     * @return array of EventObjects
     */
    public function events()
    {
        $array = $this->cal;
        $array = isset($array['VEVENT']) ? $array['VEVENT'] : array();
        $events = array();

        if (!empty($array)) {
            foreach ($array as $event) {
                $events[] = new EventObject($event);
            }
        }

        return $events;
    }

    /**
     * Returns the calendar name
     *
     * @return string
     */
    public function calendarName()
    {
        return isset($this->cal['VCALENDAR']['X-WR-CALNAME']) ? $this->cal['VCALENDAR']['X-WR-CALNAME'] : '';
    }

    /**
     * Returns the calendar description
     *
     * @return calendar description
     */
    public function calendarDescription()
    {
        return isset($this->cal['VCALENDAR']['X-WR-CALDESC']) ? $this->cal['VCALENDAR']['X-WR-CALDESC'] : '';
    }

    /**
     * Returns the calendar timezone
     *
     * @return calendar timezone
     */
    public function calendarTimeZone()
    {
        $default_timezone = date_default_timezone_get();

        if (isset($this->cal['VCALENDAR']['X-WR-TIMEZONE'])) {
            $timezone = $this->cal['VCALENDAR']['X-WR-TIMEZONE'];
        } else if (isset($this->cal['VTIMEZONE']['TZID'])) {
            $timezone = $this->cal['VTIMEZONE']['TZID'];
        } else {
            return $default_timezone;
        }

        // Use default timezone if defined is invalid
        if (!$this->isValidTimeZoneId($timezone)) {
            return $default_timezone;
        }

        return $timezone;
    }

    /**
     * Returns an array of arrays with all free/busy events. Every event is
     * an associative array and each property is an element it.
     *
     * @return array
     */
    public function freeBusyEvents()
    {
        $array = $this->cal;
        return isset($array['VFREEBUSY']) ? $array['VFREEBUSY'] : '';
    }

    /**
     * Returns a boolean value whether the current calendar has events or not
     *
     * @return boolean
     */
    public function hasEvents()
    {
        return (count($this->events()) > 0) ? true : false;
    }

    /**
     * Returns a sorted array of the events in a given range,
     * or false if no events exist in the range.
     *
     * Events will be returned if the start or end date is contained within the
     * range (inclusive), or if the event starts before and end after the range.
     *
     * If a start date is not specified or of a valid format, then the start
     * of the range will default to the current time and date of the server.
     *
     * If an end date is not specified or of a valid format, the the end of
     * the range will default to the current time and date of the server,
     * plus 20 years.
     *
     * Note that this function makes use of UNIX timestamps. This might be a
     * problem for events on, during, or after January the 29th, 2038.
     * See http://en.wikipedia.org/wiki/Unix_time#Representing_the_number
     *
     * @param string $rangeStart Start date of the search range.
     * @param string $rangeEnd   End date of the search range.
     *
     * @return array of EventObjects
     */
    public function eventsFromRange($rangeStart = false, $rangeEnd = false)
    {
        $events = $this->sortEventsWithOrder($this->events(), SORT_ASC);

        if (empty($events)) {
            return array();
        }

        $extendedEvents = array();

        if ($rangeStart) {
            try {
                $rangeStart = new \DateTime($rangeStart);
            } catch (\Exception $e) {
                error_log('ICal::eventsFromRange: Invalid date passed (' . $rangeStart . ')');
                $rangeStart = false;
            }
        }
        if (!$rangeStart) {
            $rangeStart = new \DateTime();
        }

        if ($rangeEnd) {
            try {
                $rangeEnd = new \DateTime($rangeEnd);
            } catch (\Exception $e) {
                error_log('ICal::eventsFromRange: Invalid date passed (' . $rangeEnd . ')');
                $rangeEnd = false;
            }
        }
        if (!$rangeEnd) {
            $rangeEnd = new \DateTime();
            $rangeEnd->modify('+20 years');
        }

        // If start and end are identical and are dates with no times...
        if ($rangeEnd->format('His') == 0 && $rangeStart->getTimestamp() == $rangeEnd->getTimestamp()) {
            $rangeEnd->modify('+1 day');
        }

        $rangeStart = $rangeStart->getTimestamp();
        $rangeEnd   = $rangeEnd->getTimestamp();

        foreach ($events as $anEvent) {
            $eventStart = $anEvent->dtstart_array[2];
            $eventEnd   = (isset($anEvent->dtend_array[2])) ? $anEvent->dtend_array[2] : null;

            if (($eventStart >= $rangeStart && $eventStart < $rangeEnd)         // Event start date contained in the range
                || ($eventEnd !== null
                    && (
                        ($eventEnd > $rangeStart && $eventEnd <= $rangeEnd)     // Event end date contained in the range
                        || ($eventStart < $rangeStart && $eventEnd > $rangeEnd) // Event starts before and finishes after range
                    )
                )
            ) {
                $extendedEvents[] = $anEvent;
            }
        }

        if (empty($extendedEvents)) {
            return array();
        }
        return $extendedEvents;
    }

    /**
     * Returns a sorted array of the events following a given string,
     * or false if no events exist in the range.
     *
     * @param string $interval
     *
     * @return array of EventObjects
     */
    public function eventsFromInterval($interval)
    {
        $rangeStart = new \DateTime();
        $rangeEnd   = new \DateTime();

        $dateInterval = \DateInterval::createFromDateString($interval);
        $rangeEnd->add($dateInterval);

        return $this->eventsFromRange($rangeStart->format('Y-m-d'), $rangeEnd->format('Y-m-d'));
    }

    /**
     * Sort events based on a given sort order
     *
     * @param array   $events    An array of EventObjects
     * @param integer $sortOrder Either SORT_ASC, SORT_DESC, SORT_REGULAR, SORT_NUMERIC, SORT_STRING
     *
     * @return sorted array of EventObjects
     */
    public function sortEventsWithOrder($events, $sortOrder = SORT_ASC)
    {
        $extendedEvents = array();
        $timestamp = array();

        foreach ($events as $key => $anEvent) {
            $extendedEvents[] = $anEvent;
            $timestamp[$key] = $anEvent->dtstart_array[2];
        }

        array_multisort($timestamp, $sortOrder, $extendedEvents);

        return $extendedEvents;
    }

    /**
     * Check if a timezone is valid
     *
     * @param string $timezone A timezone
     *
     * @return boolean
     */
    function isValidTimeZoneId($timezone){
        $valid = array();
        $tza = timezone_abbreviations_list();

        foreach ($tza as $zone) {
            foreach ($zone as $item) {
                $valid[$item['timezone_id']] = true;
            }
        }

        unset($valid['']);

        return (isset($valid[$timezone]));
    }
}