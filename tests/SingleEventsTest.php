<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;

class SingleEventsTest extends TestCase
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

    public function testFullDayTimeZoneBerlin()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;VALUE=DATE:20000301',
            'DTEND;VALUE=DATE:20000302',
            1,
            $checks
        );
    }

    public function testSeveralFullDaysTimeZoneBerlin()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART;VALUE=DATE:20000301',
            'DTEND;VALUE=DATE:20000304',
            1,
            $checks
        );
    }

    public function testEventTimeZoneUTC()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20180626T070000Z', 'message' => '1st event, UTC: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART:20180626T070000Z',
            'DTEND:20180626T110000Z',
            1,
            $checks
        );
    }

    public function testEventTimeZoneBerlin()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20180626T070000', 'message' => '1st event, CEST: '),
        );
        $this->assertVEVENT(
            'Europe/Berlin',
            'DTSTART:20180626T070000',
            'DTEND:20180626T110000',
            1,
            $checks
        );
    }

    public function assertVEVENT($defaultTimezone, $dtstart, $dtend, $count, $checks)
    {
        $options = $this->getOptions($defaultTimezone);

        $testIcal  = implode(PHP_EOL, $this->getIcalHeader());
        $testIcal .= PHP_EOL;
        $testIcal .= implode(PHP_EOL, $this->formatIcalEvent($dtstart, $dtend));
        $testIcal .= PHP_EOL;
        $testIcal .= implode(PHP_EOL, $this->getIcalTimezones());
        $testIcal .= PHP_EOL;
        $testIcal .= implode(PHP_EOL, $this->getIcalFooter());

        date_default_timezone_set('UTC');

        $ical = new ICal(false, $options);
        $ical->initString($testIcal);

        $events = $ical->events();

        $this->assertCount($count, $events);

        foreach ($checks as $check) {
            $this->assertEvent(
                $events[$check['index']],
                $check['dateString'],
                $check['message'],
                isset($check['timezone']) ? $check['timezone'] : $defaultTimezone
            );
        }
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
            'skipRecurrence'              => false,            // Default value
        );

        return $options;
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

    public function formatIcalEvent($dtstart, $dtend)
    {
        return array(
            'BEGIN:VEVENT',
            'CREATED:20090213T195947Z',
            'UID:M2CD-1-1-5FB000FB-BBE4-4F3F-9E7E-217F1FF97209',
            $dtstart,
            $dtend,
            'SUMMARY:test',
            'LAST-MODIFIED:20110429T222101Z',
            'DTSTAMP:20170630T105724Z',
            'SEQUENCE:0',
            'END:VEVENT',
        );
    }

    public function getIcalTimezones()
    {
        return array(
            'BEGIN:VTIMEZONE',
            'TZID:Europe/Berlin',
            'X-LIC-LOCATION:Europe/Berlin',
            'BEGIN:STANDARD',
            'DTSTART:18930401T000000',
            'RDATE:18930401T000000',
            'TZNAME:CEST',
            'TZOFFSETFROM:+005328',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19160430T230000',
            'RDATE:19160430T230000',
            'RDATE:19400401T020000',
            'RDATE:19430329T020000',
            'RDATE:19460414T020000',
            'RDATE:19470406T030000',
            'RDATE:19480418T020000',
            'RDATE:19490410T020000',
            'RDATE:19800406T020000',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'DTSTART:19161001T010000',
            'RDATE:19161001T010000',
            'RDATE:19421102T030000',
            'RDATE:19431004T030000',
            'RDATE:19441002T030000',
            'RDATE:19451118T030000',
            'RDATE:19461007T030000',
            'TZNAME:CET',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19170416T020000',
            'RRULE:FREQ=YEARLY;UNTIL=19180415T010000Z;BYMONTH=4;BYDAY=3MO',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'DTSTART:19170917T030000',
            'RRULE:FREQ=YEARLY;UNTIL=19180916T010000Z;BYMONTH=9;BYDAY=3MO',
            'TZNAME:CET',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19440403T020000',
            'RRULE:FREQ=YEARLY;UNTIL=19450402T010000Z;BYMONTH=4;BYDAY=1MO',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'END:DAYLIGHT',
            'BEGIN:DAYLIGHT',
            'DTSTART:19450524T020000',
            'RDATE:19450524T020000',
            'RDATE:19470511T030000',
            'TZNAME:CEMT',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0300',
            'END:DAYLIGHT',
            'BEGIN:DAYLIGHT',
            'DTSTART:19450924T030000',
            'RDATE:19450924T030000',
            'RDATE:19470629T030000',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0300',
            'TZOFFSETTO:+0200',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'DTSTART:19460101T000000',
            'RDATE:19460101T000000',
            'RDATE:19800101T000000',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:STANDARD',
            'DTSTART:19471005T030000',
            'RRULE:FREQ=YEARLY;UNTIL=19491002T010000Z;BYMONTH=10;BYDAY=1SU',
            'TZNAME:CET',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:STANDARD',
            'DTSTART:19800928T030000',
            'RRULE:FREQ=YEARLY;UNTIL=19950924T010000Z;BYMONTH=9;BYDAY=-1SU',
            'TZNAME:CET',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19810329T020000',
            'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'DTSTART:19961027T030000',
            'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU',
            'TZNAME:CET',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'END:VTIMEZONE',
            'BEGIN:VTIMEZONE',
            'TZID:Europe/Paris',
            'X-LIC-LOCATION:Europe/Paris',
            'BEGIN:STANDARD',
            'DTSTART:18910315T000100',
            'RDATE:18910315T000100',
            'TZNAME:PMT',
            'TZOFFSETFROM:+000921',
            'TZOFFSETTO:+000921',
            'END:STANDARD',
            'BEGIN:STANDARD',
            'DTSTART:19110311T000100',
            'RDATE:19110311T000100',
            'TZNAME:WEST',
            'TZOFFSETFROM:+000921',
            'TZOFFSETTO:+0000',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19160614T230000',
            'RDATE:19160614T230000',
            'RDATE:19170324T230000',
            'RDATE:19180309T230000',
            'RDATE:19190301T230000',
            'RDATE:19200214T230000',
            'RDATE:19210314T230000',
            'RDATE:19220325T230000',
            'RDATE:19230526T230000',
            'RDATE:19240329T230000',
            'RDATE:19250404T230000',
            'RDATE:19260417T230000',
            'RDATE:19270409T230000',
            'RDATE:19280414T230000',
            'RDATE:19290420T230000',
            'RDATE:19300412T230000',
            'RDATE:19310418T230000',
            'RDATE:19320402T230000',
            'RDATE:19330325T230000',
            'RDATE:19340407T230000',
            'RDATE:19350330T230000',
            'RDATE:19360418T230000',
            'RDATE:19370403T230000',
            'RDATE:19380326T230000',
            'RDATE:19390415T230000',
            'RDATE:19400225T020000',
            'TZNAME:WEST',
            'TZOFFSETFROM:+0000',
            'TZOFFSETTO:+0100',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'DTSTART:19161002T000000',
            'RRULE:FREQ=YEARLY;UNTIL=19191005T230000Z;BYMONTH=10;BYMONTHDAY=2,3,4,5,6,',
            ' 7,8;BYDAY=MO',
            'TZNAME:WET',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0000',
            'END:STANDARD',
            'BEGIN:STANDARD',
            'DTSTART:19201024T000000',
            'RDATE:19201024T000000',
            'RDATE:19211026T000000',
            'RDATE:19391119T000000',
            'TZNAME:WET',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0000',
            'END:STANDARD',
            'BEGIN:STANDARD',
            'DTSTART:19221008T000000',
            'RRULE:FREQ=YEARLY;UNTIL=19381001T230000Z;BYMONTH=10;BYMONTHDAY=2,3,4,5,6,',
            ' 7,8;BYDAY=SU',
            'TZNAME:WET',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0000',
            'END:STANDARD',
            'BEGIN:STANDARD',
            'DTSTART:19400614T230000',
            'RDATE:19400614T230000',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'END:STANDARD',
            'BEGIN:STANDARD',
            'DTSTART:19421102T030000',
            'RDATE:19421102T030000',
            'RDATE:19431004T030000',
            'RDATE:19760926T010000',
            'RDATE:19770925T030000',
            'RDATE:19781001T030000',
            'TZNAME:CET',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19430329T020000',
            'RDATE:19430329T020000',
            'RDATE:19440403T020000',
            'RDATE:19760328T010000',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'DTSTART:19440825T000000',
            'RDATE:19440825T000000',
            'TZNAME:WEST',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0200',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19441008T010000',
            'RDATE:19441008T010000',
            'TZNAME:WEST',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:DAYLIGHT',
            'BEGIN:DAYLIGHT',
            'DTSTART:19450402T020000',
            'RDATE:19450402T020000',
            'TZNAME:WEMT',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'DTSTART:19450916T030000',
            'RDATE:19450916T030000',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:STANDARD',
            'DTSTART:19770101T000000',
            'RDATE:19770101T000000',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19770403T020000',
            'RRULE:FREQ=YEARLY;UNTIL=19800406T010000Z;BYMONTH=4;BYDAY=1SU',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'DTSTART:19790930T030000',
            'RRULE:FREQ=YEARLY;UNTIL=19950924T010000Z;BYMONTH=9;BYDAY=-1SU',
            'TZNAME:CET',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19810329T020000',
            'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU',
            'TZNAME:CEST',
            'TZOFFSETFROM:+0100',
            'TZOFFSETTO:+0200',
            'END:DAYLIGHT',
            'BEGIN:STANDARD',
            'DTSTART:19961027T030000',
            'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU',
            'TZNAME:CET',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0100',
            'END:STANDARD',
            'END:VTIMEZONE',
            'BEGIN:VTIMEZONE',
            'TZID:US-Eastern',
            'LAST-MODIFIED:19870101T000000Z',
            'TZURL:http://zones.stds_r_us.net/tz/US-Eastern',
            'BEGIN:STANDARD',
            'DTSTART:19671029T020000',
            'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10',
            'TZOFFSETFROM:-0400',
            'TZOFFSETTO:-0500',
            'TZNAME:EST',
            'END:STANDARD',
            'BEGIN:DAYLIGHT',
            'DTSTART:19870405T020000',
            'RRULE:FREQ=YEARLY;BYDAY=1SU;BYMONTH=4',
            'TZOFFSETFROM:-0500',
            'TZOFFSETTO:-0400',
            'TZNAME:EDT',
            'END:DAYLIGHT',
            'END:VTIMEZONE',
        );
    }

    public function getIcalFooter()
    {
        return array('END:VCALENDAR');
    }

    public function assertEvent($event, $expectedDateString, $message, $timezone = null)
    {
        if ($timezone !== null) {
            date_default_timezone_set($timezone);
        }

        $expectedTimeStamp = strtotime($expectedDateString);

        $this->assertEquals(
            $expectedTimeStamp,
            $event->dtstart_array[2],
            $message . 'timestamp mismatch (expected ' . $expectedDateString . ' vs actual ' . $event->dtstart . ')'
        );
        $this->assertAttributeEquals(
            $expectedDateString,
            'dtstart',
            $event,
            $message . 'dtstart mismatch (timestamp is okay)'
        );
    }

    public function assertEventFile($defaultTimezone, $file, $count, $checks)
    {
        $options = $this->getOptions($defaultTimezone);

        date_default_timezone_set('UTC');

        $ical = new ICal($file, $options);

        $events = $ical->events();

        $this->assertCount($count, $events);

        foreach ($checks as $check) {
            $this->assertEvent(
                $events[$check['index']],
                $check['dateString'],
                $check['message'],
                isset($check['timezone']) ? $check['timezone'] : $defaultTimezone
            );
        }
    }
}
