<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;

class RecurrencesTest extends TestCase
{
    // phpcs:disable Generic.Arrays.DisallowLongArraySyntax
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Squiz.Commenting.FunctionComment
    // phpcs:disable Squiz.Commenting.VariableComment

    private $originalTimeZone = null;

    public function setUp()
    {
        $this->originalTimeZone = date_default_timezone_get();
    }

    public function tearDown()
    {
        date_default_timezone_set($this->originalTimeZone);
    }

    public function testYearlyFullDayTimeZoneBerlin()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '),
            array('index' => 1, 'dateString' => '20010301T000000', 'message' => '2nd event, CET: '),
            array('index' => 2, 'dateString' => '20020301T000000', 'message' => '3rd event, CET: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;VALUE=DATE:20000301',
            'DTEND;VALUE=DATE:20000302',
            'RRULE:FREQ=YEARLY;WKST=SU;COUNT=3',
            3,
            $checks
        );
    }

    public function testMonthlyFullDayTimeZoneBerlin()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '),
            array('index' => 1, 'dateString' => '20000401T000000', 'message' => '2nd event, CEST: '),
            array('index' => 2, 'dateString' => '20000501T000000', 'message' => '3rd event, CEST: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;VALUE=DATE:20000301',
            'DTEND;VALUE=DATE:20000302',
            'RRULE:FREQ=MONTHLY;BYMONTHDAY=1;WKST=SU;COUNT=3',
            3,
            $checks
        );
    }

    public function testMonthlyFullDayTimeZoneBerlinSummerTime()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20180701', 'message' => '1st event, CEST: '),
            array('index' => 1, 'dateString' => '20180801T000000', 'message' => '2nd event, CEST: '),
            array('index' => 2, 'dateString' => '20180901T000000', 'message' => '3rd event, CEST: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;VALUE=DATE:20180701',
            'DTEND;VALUE=DATE:20180702',
            'RRULE:FREQ=MONTHLY;BYMONTHDAY=1;WKST=SU;COUNT=3',
            3,
            $checks
        );
    }

    public function testMonthlyFullDayTimeZoneBerlinFromFile()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20180701', 'message' => '1st event, CEST: '),
            array('index' => 1, 'dateString' => '20180801T000000', 'message' => '2nd event, CEST: '),
            array('index' => 2, 'dateString' => '20180901T000000', 'message' => '3rd event, CEST: '),
        );
        $this->assertEventFile(
            'Europe/Berlin',
            './tests/ical/ical-monthly.ics',
            25,
            $checks
        );
    }

    public function testIssue196FromFile()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20191105T190000', 'timezone' => 'Europe/Berlin', 'message' => '1st event, CEST: '),
            array('index' => 1, 'dateString' => '20191106T190000', 'timezone' => 'Europe/Berlin', 'message' => '2nd event, CEST: '),
            array('index' => 2, 'dateString' => '20191107T190000', 'timezone' => 'Europe/Berlin', 'message' => '3rd event, CEST: '),
            array('index' => 3, 'dateString' => '20191108T190000', 'timezone' => 'Europe/Berlin', 'message' => '4th event, CEST: '),
            array('index' => 4, 'dateString' => '20191109T170000', 'timezone' => 'Europe/Berlin', 'message' => '5th event, CEST: '),
            array('index' => 5, 'dateString' => '20191110T180000', 'timezone' => 'Europe/Berlin', 'message' => '6th event, CEST: '),
        );
        $this->assertEventFile(
            'UTC',
            './tests/ical/issue-196.ics',
            7,
            $checks
        );
    }

    public function testWeeklyFullDayTimeZoneBerlin()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '),
            array('index' => 1, 'dateString' => '20000308T000000', 'message' => '2nd event, CET: '),
            array('index' => 2, 'dateString' => '20000315T000000', 'message' => '3rd event, CET: '),
            array('index' => 3, 'dateString' => '20000322T000000', 'message' => '4th event, CET: '),
            array('index' => 4, 'dateString' => '20000329T000000', 'message' => '5th event, CEST: '),
            array('index' => 5, 'dateString' => '20000405T000000', 'message' => '6th event, CEST: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;VALUE=DATE:20000301',
            'DTEND;VALUE=DATE:20000302',
            'RRULE:FREQ=WEEKLY;WKST=SU;COUNT=6',
            6,
            $checks
        );
    }

    public function testDailyFullDayTimeZoneBerlin()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '),
            array('index' => 1, 'dateString' => '20000302T000000', 'message' => '2nd event, CET: '),
            array('index' => 30, 'dateString' => '20000331T000000', 'message' => '31st event, CEST: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;VALUE=DATE:20000301',
            'DTEND;VALUE=DATE:20000302',
            'RRULE:FREQ=DAILY;WKST=SU;COUNT=31',
            31,
            $checks
        );
    }

    public function testWeeklyFullDayTimeZoneBerlinLocal()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20000301T000000', 'message' => '1st event, CET: '),
            array('index' => 1, 'dateString' => '20000308T000000', 'message' => '2nd event, CET: '),
            array('index' => 2, 'dateString' => '20000315T000000', 'message' => '3rd event, CET: '),
            array('index' => 3, 'dateString' => '20000322T000000', 'message' => '4th event, CET: '),
            array('index' => 4, 'dateString' => '20000329T000000', 'message' => '5th event, CEST: '),
            array('index' => 5, 'dateString' => '20000405T000000', 'message' => '6th event, CEST: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;TZID=Europe/Berlin:20000301T000000',
            'DTEND;TZID=Europe/Berlin:20000302T000000',
            'RRULE:FREQ=WEEKLY;WKST=SU;COUNT=6',
            6,
            $checks
        );
    }

    public function testRFCDaily10NewYork()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'timezone' => 'America/New_York', 'message' => '1st event, EDT: '),
            array('index' => 1, 'dateString' => '19970903T090000', 'timezone' => 'America/New_York', 'message' => '2nd event, EDT: '),
            array('index' => 9, 'dateString' => '19970911T090000', 'timezone' => 'America/New_York', 'message' => '10th event, EDT: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;TZID=America/New_York:19970902T090000',
            '',
            'RRULE:FREQ=DAILY;COUNT=10',
            10,
            $checks
        );
    }

    public function testRFCDaily10Berlin()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'timezone' => 'Europe/Berlin', 'message' => '1st event, CEST: '),
            array('index' => 1, 'dateString' => '19970903T090000', 'timezone' => 'Europe/Berlin', 'message' => '2nd event, CEST: '),
            array('index' => 9, 'dateString' => '19970911T090000', 'timezone' => 'Europe/Berlin', 'message' => '10th event, CEST: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;TZID=Europe/Berlin:19970902T090000',
            '',
            'RRULE:FREQ=DAILY;COUNT=10',
            10,
            $checks
        );
    }

    public function testRFCDaily10BerlinFromNewYork()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'timezone' => 'Europe/Berlin', 'message' => '1st event, CEST: '),
            array('index' => 1, 'dateString' => '19970903T090000', 'timezone' => 'Europe/Berlin', 'message' => '2nd event, CEST: '),
            array('index' => 9, 'dateString' => '19970911T090000', 'timezone' => 'Europe/Berlin', 'message' => '10th event, CEST: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            'DTSTART;TZID=Europe/Berlin:19970902T090000',
            '',
            'RRULE:FREQ=DAILY;COUNT=10',
            10,
            $checks
        );
    }

    public function assertVEVENT($defaultTimezone, $dtstart, $dtend, $rrule, $count, $checks)
    {
        $options = $this->getOptions($defaultTimezone);

        $testIcal  = implode(PHP_EOL, $this->getIcalHeader());
        $testIcal .= PHP_EOL;
        $testIcal .= implode(PHP_EOL, $this->formatIcalEvent($dtstart, $dtend, $rrule));
        $testIcal .= PHP_EOL;
        $testIcal .= implode(PHP_EOL, $this->getIcalFooter());

        $ical = new ICal(false, $options);
        $ical->initString($testIcal);

        $events = $ical->events();

        $this->assertCount($count, $events);

        foreach ($checks as $check) {
            $this->assertEvent($events[$check['index']], $check['dateString'], $check['message'], isset($check['timezone']) ? $check['timezone'] : $defaultTimezone);
        }
    }

    public function assertEventFile($defaultTimezone, $file, $count, $checks)
    {
        $options = $this->getOptions($defaultTimezone);

        $ical = new ICal($file, $options);

        $events = $ical->events();

        $this->assertCount($count, $events);

        $events = $ical->sortEventsWithOrder($events);

        foreach ($checks as $check) {
            $this->assertEvent($events[$check['index']], $check['dateString'], $check['message'], isset($check['timezone']) ? $check['timezone'] : $defaultTimezone);
        }
    }

    public function assertEvent($event, $expectedDateString, $message, $timeZone = null)
    {
        if (!is_null($timeZone)) {
            date_default_timezone_set($timeZone);
        }

        $expectedTimeStamp = strtotime($expectedDateString);

        $this->assertEquals($expectedTimeStamp, $event->dtstart_array[2], $message . 'timestamp mismatch (expected ' . $expectedDateString . ' vs actual ' . $event->dtstart . ')');
        $this->assertAttributeEquals($expectedDateString, 'dtstart', $event, $message . 'dtstart mismatch (timestamp is okay)');
    }

    public function getOptions($defaultTimezone)
    {
        $options = array(
            'defaultSpan'                 => 2,                            // Default value
            'defaultTimeZone'             => $defaultTimezone,             // Default value: UTC
            'defaultWeekStart'            => 'MO',                         // Default value
            'disableCharacterReplacement' => false,                        // Default value
            'filterDaysAfter'             => null,                         // Default value
            'filterDaysBefore'            => null,                         // Default value
            'skipRecurrence'              => false,                        // Default value
        );

        return $options;
    }

    public function formatIcalEvent($dtstart, $dtend, $rrule)
    {
        return array(
            'BEGIN:VEVENT',
            'CREATED:20090213T195947Z',
            'UID:M2CD-1-1-5FB000FB-BBE4-4F3F-9E7E-217F1FF97209',
            $rrule,
            $dtstart,
            $dtend,
            'SUMMARY:test',
            'LAST-MODIFIED:20110429T222101Z',
            'DTSTAMP:20170630T105724Z',
            'SEQUENCE:0',
            'END:VEVENT',
        );
    }

    public function getIcalHeader()
    {
        return array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Google Inc//Google Calendar 70.9054//EN',
            'X-WR-CALNAME:Private',
            'X-APPLE-CALENDAR-COLOR:#FF2968',
            'X-WR-CALDESC:',
        );
    }

    public function getIcalFooter()
    {
        return array('END:VCALENDAR');
    }
}
