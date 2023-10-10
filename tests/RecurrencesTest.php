<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;

class RecurrencesTest extends TestCase
{
    // phpcs:disable Generic.Arrays.DisallowLongArraySyntax
    // phpcs:disable Squiz.Commenting.FunctionComment
    // phpcs:disable Squiz.Commenting.VariableComment

    private $originalTimeZone = null;

    /**
     * @before
     */
    public function setUpFixtures()
    {
        $this->originalTimeZone = date_default_timezone_get();
    }

    /**
     * @after
     */
    public function tearDownFixtures()
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
            array(
                'DTSTART;VALUE=DATE:20000301',
                'DTEND;VALUE=DATE:20000302',
                'RRULE:FREQ=YEARLY;WKST=SU;COUNT=3',
            ),
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
            array(
                'DTSTART;VALUE=DATE:20000301',
                'DTEND;VALUE=DATE:20000302',
                'RRULE:FREQ=MONTHLY;BYMONTHDAY=1;WKST=SU;COUNT=3',
            ),
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
            array(
                'DTSTART;VALUE=DATE:20180701',
                'DTEND;VALUE=DATE:20180702',
                'RRULE:FREQ=MONTHLY;WKST=SU;COUNT=3',
            ),
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
            array(
                'DTSTART;VALUE=DATE:20000301',
                'DTEND;VALUE=DATE:20000302',
                'RRULE:FREQ=WEEKLY;WKST=SU;COUNT=6',
            ),
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
            array(
                'DTSTART;VALUE=DATE:20000301',
                'DTEND;VALUE=DATE:20000302',
                'RRULE:FREQ=DAILY;WKST=SU;COUNT=31',
            ),
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
            array(
                'DTSTART;TZID=Europe/Berlin:20000301T000000',
                'DTEND;TZID=Europe/Berlin:20000302T000000',
                'RRULE:FREQ=WEEKLY;WKST=SU;COUNT=6',
            ),
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
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;COUNT=10',
            ),
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
            array(
                'DTSTART;TZID=Europe/Berlin:19970902T090000',
                'RRULE:FREQ=DAILY;COUNT=10',
            ),
            10,
            $checks
        );
    }

    public function testStartDateIsExdateUsingUntil()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20190918T095000', 'timezone' => 'Europe/London', 'message' => '1st event: '),
            array('index' => 1, 'dateString' => '20191002T095000', 'timezone' => 'Europe/London', 'message' => '2nd event: '),
            array('index' => 2, 'dateString' => '20191016T095000', 'timezone' => 'Europe/London', 'message' => '3rd event: '),
        );
        $this->assertVEVENT(
            'Europe/London',
            array(
                'DTSTART;TZID=Europe/London:20190911T095000',
                'RRULE:FREQ=WEEKLY;WKST=SU;UNTIL=20191027T235959Z;BYDAY=WE',
                'EXDATE;TZID=Europe/London:20191023T095000',
                'EXDATE;TZID=Europe/London:20191009T095000',
                'EXDATE;TZID=Europe/London:20190925T095000',
                'EXDATE;TZID=Europe/London:20190911T095000',
            ),
            3,
            $checks
        );
    }

    public function testStartDateIsExdateUsingCount()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20190918T095000', 'timezone' => 'Europe/London', 'message' => '1st event: '),
            array('index' => 1, 'dateString' => '20191002T095000', 'timezone' => 'Europe/London', 'message' => '2nd event: '),
            array('index' => 2, 'dateString' => '20191016T095000', 'timezone' => 'Europe/London', 'message' => '3rd event: '),
        );
        $this->assertVEVENT(
            'Europe/London',
            array(
                'DTSTART;TZID=Europe/London:20190911T095000',
                'RRULE:FREQ=WEEKLY;WKST=SU;COUNT=7;BYDAY=WE',
                'EXDATE;TZID=Europe/London:20191023T095000',
                'EXDATE;TZID=Europe/London:20191009T095000',
                'EXDATE;TZID=Europe/London:20190925T095000',
                'EXDATE;TZID=Europe/London:20190911T095000',
            ),
            3,
            $checks
        );
    }

    public function testCountWithExdate()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20200323T050000', 'timezone' => 'Europe/Paris', 'message' => '1st event: '),
            array('index' => 1, 'dateString' => '20200324T050000', 'timezone' => 'Europe/Paris', 'message' => '2nd event: '),
            array('index' => 2, 'dateString' => '20200327T050000', 'timezone' => 'Europe/Paris', 'message' => '3rd event: '),
        );
        $this->assertVEVENT(
            'Europe/London',
            array(
                'DTSTART;TZID=Europe/Paris:20200323T050000',
                'DTEND;TZID=Europe/Paris:20200323T070000',
                'RRULE:FREQ=DAILY;COUNT=5',
                'EXDATE;TZID=Europe/Paris:20200326T050000',
                'EXDATE;TZID=Europe/Paris:20200325T050000',
                'DTSTAMP:20200318T141057Z',
            ),
            3,
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
            array(
                'DTSTART;TZID=Europe/Berlin:19970902T090000',
                'RRULE:FREQ=DAILY;COUNT=10',
            ),
            10,
            $checks
        );
    }

    public function testExdatesInDifferentTimezone()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20170503T190000', 'message' => '1st event: '),
            array('index' => 1, 'dateString' => '20170510T190000', 'message' => '2nd event: '),
            array('index' => 9, 'dateString' => '20170712T190000', 'message' => '10th event: '),
            array('index' => 19, 'dateString' => '20171004T190000', 'message' => '20th event: '),
        );
        $this->assertVEVENT(
            'America/Chicago',
            array(
                'DTSTART;TZID=America/Chicago:20170503T190000',
                'RRULE:FREQ=WEEKLY;BYDAY=WE;WKST=SU;UNTIL=20180101',
                'EXDATE:20170601T000000Z',
                'EXDATE:20170803T000000Z',
                'EXDATE:20170824T000000Z',
                'EXDATE:20171026T000000Z',
                'EXDATE:20171102T000000Z',
                'EXDATE:20171123T010000Z',
                'EXDATE:20171221T010000Z',
            ),
            28,
            $checks
        );
    }

    public function testYearlyWithBySetPos()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970306T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970313T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970325T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19980305T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19980312T090000', 'message' => '5th occurrence: '),
            array('index' => 5, 'dateString' => '19980326T090000', 'message' => '6th occurrence: '),
            array('index' => 9, 'dateString' => '20000307T090000', 'message' => '10th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970306T090000',
                'RRULE:FREQ=YEARLY;COUNT=10;BYMONTH=3;BYDAY=TU,TH;BYSETPOS=2,4,-2',
            ),
            10,
            $checks
        );
    }

    public function testDailyWithByMonthDay()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20000206T120000', 'message' => '1st event: '),
            array('index' => 1, 'dateString' => '20000211T120000', 'message' => '2nd event: '),
            array('index' => 2, 'dateString' => '20000216T120000', 'message' => '3rd event: '),
            array('index' => 4, 'dateString' => '20000226T120000', 'message' => '5th event, transition from February to March: '),
            array('index' => 5, 'dateString' => '20000301T120000', 'message' => '6th event, transition to March from February: '),
            array('index' => 11, 'dateString' => '20000331T120000', 'message' => '12th event, transition from March to April: '),
            array('index' => 12, 'dateString' => '20000401T120000', 'message' => '13th event, transition to April from March: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            array(
                'DTSTART:20000206T120000',
                'DTEND:20000206T130000',
                'RRULE:FREQ=DAILY;BYMONTHDAY=1,6,11,16,21,26,31;COUNT=16',
            ),
            16,
            $checks
        );
    }

    public function testYearlyWithByMonthDay()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20001214T120000', 'message' => '1st event: '),
            array('index' => 1, 'dateString' => '20001221T120000', 'message' => '2nd event: '),
            array('index' => 2, 'dateString' => '20010107T120000', 'message' => '3rd event: '),
            array('index' => 3, 'dateString' => '20010114T120000', 'message' => '4th event: '),
            array('index' => 6, 'dateString' => '20010214T120000', 'message' => '7th event: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            array(
                'DTSTART:20001214T120000',
                'DTEND:20001214T130000',
                'RRULE:FREQ=YEARLY;BYMONTHDAY=7,14,21;COUNT=8',
            ),
            8,
            $checks
        );
    }

    public function testYearlyWithByMonthDayAndByDay()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20001214T120000', 'message' => '1st event: '),
            array('index' => 1, 'dateString' => '20001221T120000', 'message' => '2nd event: '),
            array('index' => 2, 'dateString' => '20010607T120000', 'message' => '3rd event: '),
            array('index' => 3, 'dateString' => '20010614T120000', 'message' => '4th event: '),
            array('index' => 6, 'dateString' => '20020214T120000', 'message' => '7th event: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            array(
                'DTSTART:20001214T120000',
                'DTEND:20001214T130000',
                'RRULE:FREQ=YEARLY;BYMONTHDAY=7,14,21;BYDAY=TH;COUNT=8',
            ),
            8,
            $checks
        );
    }

    public function testYearlyWithByMonthAndByMonthDay()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20001214T120000', 'message' => '1st event: '),
            array('index' => 1, 'dateString' => '20001221T120000', 'message' => '2nd event: '),
            array('index' => 2, 'dateString' => '20010607T120000', 'message' => '3rd event: '),
            array('index' => 3, 'dateString' => '20010614T120000', 'message' => '4th event: '),
            array('index' => 6, 'dateString' => '20011214T120000', 'message' => '7th event: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            array(
                'DTSTART:20001214T120000',
                'DTEND:20001214T130000',
                'RRULE:FREQ=YEARLY;BYMONTH=12,6;BYMONTHDAY=7,14,21;COUNT=8',
            ),
            8,
            $checks
        );
    }

    public function testCountIsOne()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20211201T090000', 'message' => '1st and only expected event: '),
        );
        $this->assertVEVENT(
            'UTC',
            array(
                'DTSTART:20211201T090000',
                'DTEND:20211201T100000',
                'RRULE:FREQ=DAILY;COUNT=1',
            ),
            1,
            $checks
        );
    }

    public function test5thByDayOfMonth()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20200103T090000', 'message' => '1st event: '),
            array('index' => 1, 'dateString' => '20200129T090000', 'message' => '2nd event: '),
            array('index' => 2, 'dateString' => '20200429T090000', 'message' => '3rd event: '),
            array('index' => 3, 'dateString' => '20200501T090000', 'message' => '4th event: '),
            array('index' => 4, 'dateString' => '20200703T090000', 'message' => '5th event: '),
            array('index' => 5, 'dateString' => '20200729T090000', 'message' => '6th event: '),
            array('index' => 6, 'dateString' => '20200930T090000', 'message' => '7th event: '),
            array('index' => 7, 'dateString' => '20201002T090000', 'message' => '8th event: '),
            array('index' => 8, 'dateString' => '20201230T090000', 'message' => '9th event: '),
            array('index' => 9, 'dateString' => '20210101T090000', 'message' => '10th and last event: '),
        );
        $this->assertVEVENT(
            'UTC',
            array(
                'DTSTART:20200103T090000',
                'DTEND:20200103T100000',
                'RRULE:FREQ=MONTHLY;BYDAY=5WE,-5FR;UNTIL=20210102T090000',
            ),
            10,
            $checks
        );
    }

    public function assertVEVENT($defaultTimezone, $veventParts, $count, $checks)
    {
        $options = $this->getOptions($defaultTimezone);

        $testIcal  = implode(PHP_EOL, $this->getIcalHeader());
        $testIcal .= PHP_EOL;
        $testIcal .= implode(PHP_EOL, $this->formatIcalEvent($veventParts));
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

        $this->assertSame($expectedTimeStamp, $event->dtstart_array[2], $message . 'timestamp mismatch (expected ' . $expectedDateString . ' vs actual ' . $event->dtstart . ')');
        $this->assertSame($expectedDateString, $event->dtstart, $message . 'dtstart mismatch (timestamp is okay)');
    }

    public function getOptions($defaultTimezone)
    {
        $options = array(
            'defaultSpan'                 => 2,                // Default value
            'defaultTimeZone'             => $defaultTimezone, // Default value: UTC
            'defaultWeekStart'            => 'MO',             // Default value
            'disableCharacterReplacement' => false,            // Default value
            'filterDaysAfter'             => null,             // Default value
            'filterDaysBefore'            => null,             // Default value
            'httpUserAgent'               => null,             // Default value
            'skipRecurrence'              => false,            // Default value
        );

        return $options;
    }

    public function formatIcalEvent($veventParts)
    {
        return array_merge(
            array(
                'BEGIN:VEVENT',
                'CREATED:' . gmdate('Ymd\THis\Z'),
                'UID:M2CD-1-1-5FB000FB-BBE4-4F3F-9E7E-217F1FF97209',
            ),
            $veventParts,
            array(
                'SUMMARY:test',
                'LAST-MODIFIED:' . gmdate('Ymd\THis\Z', filemtime(__FILE__)),
                'END:VEVENT',
            )
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
