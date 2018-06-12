<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;

class RecurrencesTest extends TestCase
{

  private $useTimeZoneWithRRules = false;

  /**
   * @runInSeparateProcess
   */
  public function testYearlyFullDayTimeZoneBerlin() {
    $checks = [
      ['index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '],
      ['index' => 1, 'dateString' => '20010301T000000', 'message' => '2nd event, CET: '],
      ['index' => 2, 'dateString' => '20020301T000000', 'message' => '3rd event, CET: '],
    ];
    $this->assertVEVENT(
      'Europe/Berlin',
      "DTSTART;VALUE=DATE:20000301",
      "DTEND;VALUE=DATE:20000302",
      "RRULE:FREQ=YEARLY;WKST=SU;COUNT=3",
      3,
      $checks);
  }

  /**
   * @runInSeparateProcess
   */
  public function testMonthlyFullDayTimeZoneBerlin() {
    $checks = [
      ['index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '],
      ['index' => 1, 'dateString' => '20000401T000000', 'message' => '2nd event, CEST: '],
      ['index' => 2, 'dateString' => '20000501T000000', 'message' => '3rd event, CEST: '],
    ];
    $this->assertVEVENT(
      'Europe/Berlin',
      "DTSTART;VALUE=DATE:20000301",
      "DTEND;VALUE=DATE:20000302",
      "RRULE:FREQ=MONTHLY;BYMONTHDAY=1;WKST=SU;COUNT=3",
      3,
      $checks);
  }

  /**
   * @runInSeparateProcess
   */
  public function testMonthlyFullDayTimeZoneBerlinSummerTime() {
    $checks = [
      ['index' => 0, 'dateString' => '20180701', 'message' => '1st event, CEST: '],
      ['index' => 1, 'dateString' => '20180801T000000', 'message' => '2nd event, CEST: '],
      ['index' => 2, 'dateString' => '20180901T000000', 'message' => '3rd event, CEST: '],
    ];
    $this->assertVEVENT(
      'Europe/Berlin',
      "DTSTART;VALUE=DATE:20180701",
      "DTEND;VALUE=DATE:20180702",
      "RRULE:FREQ=MONTHLY;BYMONTHDAY=1;WKST=SU;COUNT=3",
      3,
      $checks);
  }

  /**
   * @runInSeparateProcess
   */
  public function testMonthlyFullDayTimeZoneBerlinFromFile() {
    $checks = [
      ['index' => 0, 'dateString' => '20180701', 'message' => '1st event, CEST: '],
      ['index' => 1, 'dateString' => '20180801T000000', 'message' => '2nd event, CEST: '],
      ['index' => 2, 'dateString' => '20180901T000000', 'message' => '3rd event, CEST: '],
    ];
    $this->assertEventFile(
      'Europe/Berlin',
      "./tests/icalmonthly.txt",
      24,
      $checks);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWeeklyFullDayTimeZoneBerlin() {
    $checks = [
      ['index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '],
      ['index' => 1, 'dateString' => '20000308T000000', 'message' => '2nd event, CET: '],
      ['index' => 2, 'dateString' => '20000315T000000', 'message' => '3rd event, CET: '],
      ['index' => 3, 'dateString' => '20000322T000000', 'message' => '4th event, CET: '],
      ['index' => 4, 'dateString' => '20000329T000000', 'message' => '5th event, CEST: '],
      ['index' => 5, 'dateString' => '20000405T000000', 'message' => '6th event, CEST: '],
    ];
    $this->assertVEVENT(
      'Europe/Berlin',
      "DTSTART;VALUE=DATE:20000301",
      "DTEND;VALUE=DATE:20000302",
      "RRULE:FREQ=WEEKLY;WKST=SU;COUNT=6",
      6,
      $checks);
  }

  /**
   * @runInSeparateProcess
   */
  public function testDailyFullDayTimeZoneBerlin() {
    $checks = [
      ['index' => 0, 'dateString' => '20000301', 'message' => '1st event, CET: '],
      ['index' => 1, 'dateString' => '20000302T000000', 'message' => '2nd event, CET: '],
      ['index' => 30, 'dateString' => '20000331T000000', 'message' => '31st event, CEST: '],
    ];
    $this->assertVEVENT(
      'Europe/Berlin',
      "DTSTART;VALUE=DATE:20000301",
      "DTEND;VALUE=DATE:20000302",
      "RRULE:FREQ=DAILY;WKST=SU;COUNT=31",
      31,
      $checks);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWeeklyFullDayTimeZoneBerlinLocal() {
    $checks = [
      ['index' => 0, 'dateString' => '20000301T000000', 'message' => '1st event, CET: '],
      ['index' => 1, 'dateString' => '20000308T000000', 'message' => '2nd event, CET: '],
      ['index' => 2, 'dateString' => '20000315T000000', 'message' => '3rd event, CET: '],
      ['index' => 3, 'dateString' => '20000322T000000', 'message' => '4th event, CET: '],
      ['index' => 4, 'dateString' => '20000329T000000', 'message' => '5th event, CEST: '],
      ['index' => 5, 'dateString' => '20000405T000000', 'message' => '6th event, CEST: '],
    ];
    $this->assertVEVENT(
      'Europe/Berlin',
      "DTSTART;TZID=Europe/Berlin:20000301T000000",
      "DTEND;TZID=Europe/Berlin:20000302T000000",
      "RRULE:FREQ=WEEKLY;WKST=SU;COUNT=6",
      6,
      $checks);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRFCDaily10NewYork() {
    // (1997 9:00 AM EDT)September 2-11
    $checks = [
      ['index' => 0, 'dateString' => '19970902T090000', 'timezone' => 'America/New_York', 'message' => '1st event, EDT: '],
      ['index' => 1, 'dateString' => '19970903T090000', 'timezone' => 'America/New_York', 'message' => '2nd event, EDT: '],
      ['index' => 9, 'dateString' => '19970911T090000', 'timezone' => 'America/New_York', 'message' => '10th event, EDT: '],
    ];
    $this->assertVEVENT(
      'Europe/Berlin',
      "DTSTART;TZID=America/New_York:19970902T090000",
      "",
      "RRULE:FREQ=DAILY;COUNT=10",
      10,
      $checks);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRFCDaily10Berlin() {
    // (1997 9:00 AM CEST)September 2-11
    $checks = [
      ['index' => 0, 'dateString' => '19970902T090000', 'timezone' => 'Europe/Berlin', 'message' => '1st event, CEST: '],
      ['index' => 1, 'dateString' => '19970903T090000', 'timezone' => 'Europe/Berlin', 'message' => '2nd event, CEST: '],
      ['index' => 9, 'dateString' => '19970911T090000', 'timezone' => 'Europe/Berlin', 'message' => '10th event, CEST: '],
    ];
    $this->assertVEVENT(
      'Europe/Berlin',
      "DTSTART;TZID=Europe/Berlin:19970902T090000",
      "",
      "RRULE:FREQ=DAILY;COUNT=10",
      10,
      $checks);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRFCDaily10BerlinFromNewYork() {
    // (1997 9:00 AM CEST)September 2-11
    $checks = [
      ['index' => 0, 'dateString' => '19970902T090000', 'timezone' => 'Europe/Berlin', 'message' => '1st event, CEST: '],
      ['index' => 1, 'dateString' => '19970903T090000', 'timezone' => 'Europe/Berlin', 'message' => '2nd event, CEST: '],
      ['index' => 9, 'dateString' => '19970911T090000', 'timezone' => 'Europe/Berlin', 'message' => '10th event, CEST: '],
    ];
    $this->assertVEVENT(
      'America/New_York',
      "DTSTART;TZID=Europe/Berlin:19970902T090000",
      "",
      "RRULE:FREQ=DAILY;COUNT=10",
      10,
      $checks);
  }

  /* ********************************************************************************************* */

  function assertVEVENT($defaultTimezone, $dtstart, $dtend, $rrule, $count, $checks) {
    $options = $this->getOptions($defaultTimezone);
    $testIcal = $this->getIcalHeader() .
      $this->formatIcalEvent($dtstart, $dtend, $rrule) .
      $this->getIcalTimezones() .
      $this->getIcalFooter();

    $ical = new ICal(false, $options);
    $ical->initString($testIcal);

    $events = $ical->events();

    $this->assertCount($count, $events);

    foreach($checks as $check) {
      //echo $events[$check['index']]->dtstart_array[3].PHP_EOL;

      $this->assertEvent($events[$check['index']], $check['dateString'], $check['message'], isset($check['timezone']) ? $check['timezone'] : $defaultTimezone);
    }
  }

  function assertEventFile($defaultTimezone, $file, $count, $checks) {
    $options = $this->getOptions($defaultTimezone);

    $ical = new ICal($file, $options);

    $events = $ical->events();

    $this->assertCount($count, $events);

    foreach($checks as $check) {
      $this->assertEvent($events[$check['index']], $check['dateString'], $check['message'], isset($check['timezone']) ? $check['timezone'] : $defaultTimezone);
    }
  }

  function assertEvent($event, $expectedDateString, $message, $timezone = null) {
    if ($timezone !== null) {
      date_default_timezone_set($timezone);
    }
    $expectedTimeStamp = strtotime($expectedDateString);

    $this->assertEquals($expectedTimeStamp, $event->dtstart_array[2], $message . 'timestamp mismatch (expected '.$expectedDateString.' vs actual '.$event->dtstart.')');
    $this->assertAttributeEquals($expectedDateString, 'dtstart', $event, $message . 'dtstart mismatch (timestamp is okay)');
  }

  function getOptions($defaultTimezone) {
    $options = array(
      'defaultSpan'                 => 2,     // Default value
      'defaultTimeZone'             => $defaultTimezone, // Default value: UTC
      'defaultWeekStart'            => 'MO',  // Default value
      'disableCharacterReplacement' => false, // Default value
      'skipRecurrence'              => false, // Default value
      'useTimeZoneWithRRules'       => $this->useTimeZoneWithRRules, // Default value: false
    );
    return $options;
  }

  function formatIcalEvent($dtstart, $dtend, $rrule) {
    return "BEGIN:VEVENT
CREATED:20090213T195947Z
UID:M2CD-1-1-5FB000FB-BBE4-4F3F-9E7E-217F1FF97209
" . $rrule . PHP_EOL . $dtstart . PHP_EOL . $dtend . PHP_EOL .
"SUMMARY:test
LAST-MODIFIED:20110429T222101Z
DTSTAMP:20170630T105724Z
SEQUENCE:0
END:VEVENT
";
  }

  function getIcalHeader()
  {
    return "BEGIN:VCALENDAR
VERSION:2.0
X-WR-CALNAME:Privat
X-APPLE-CALENDAR-COLOR:#FF2968
X-WR-CALDESC:
";
  }

  function getIcalTimezones()
  {
    return "BEGIN:VTIMEZONE
TZID:Europe/Berlin
X-LIC-LOCATION:Europe/Berlin
BEGIN:STANDARD
DTSTART:18930401T000000
RDATE:18930401T000000
TZNAME:CEST
TZOFFSETFROM:+005328
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19160430T230000
RDATE:19160430T230000
RDATE:19400401T020000
RDATE:19430329T020000
RDATE:19460414T020000
RDATE:19470406T030000
RDATE:19480418T020000
RDATE:19490410T020000
RDATE:19800406T020000
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19161001T010000
RDATE:19161001T010000
RDATE:19421102T030000
RDATE:19431004T030000
RDATE:19441002T030000
RDATE:19451118T030000
RDATE:19461007T030000
TZNAME:CET
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19170416T020000
RRULE:FREQ=YEARLY;UNTIL=19180415T010000Z;BYMONTH=4;BYDAY=3MO
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19170917T030000
RRULE:FREQ=YEARLY;UNTIL=19180916T010000Z;BYMONTH=9;BYDAY=3MO
TZNAME:CET
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19440403T020000
RRULE:FREQ=YEARLY;UNTIL=19450402T010000Z;BYMONTH=4;BYDAY=1MO
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:DAYLIGHT
DTSTART:19450524T020000
RDATE:19450524T020000
RDATE:19470511T030000
TZNAME:CEMT
TZOFFSETFROM:+0200
TZOFFSETTO:+0300
END:DAYLIGHT
BEGIN:DAYLIGHT
DTSTART:19450924T030000
RDATE:19450924T030000
RDATE:19470629T030000
TZNAME:CEST
TZOFFSETFROM:+0300
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19460101T000000
RDATE:19460101T000000
RDATE:19800101T000000
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0100
END:STANDARD
BEGIN:STANDARD
DTSTART:19471005T030000
RRULE:FREQ=YEARLY;UNTIL=19491002T010000Z;BYMONTH=10;BYDAY=1SU
TZNAME:CET
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:STANDARD
DTSTART:19800928T030000
RRULE:FREQ=YEARLY;UNTIL=19950924T010000Z;BYMONTH=9;BYDAY=-1SU
TZNAME:CET
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19810329T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19961027T030000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
TZNAME:CET
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
END:VTIMEZONE
BEGIN:VTIMEZONE
TZID:Europe/Paris
X-LIC-LOCATION:Europe/Paris
BEGIN:STANDARD
DTSTART:18910315T000100
RDATE:18910315T000100
TZNAME:PMT
TZOFFSETFROM:+000921
TZOFFSETTO:+000921
END:STANDARD
BEGIN:STANDARD
DTSTART:19110311T000100
RDATE:19110311T000100
TZNAME:WEST
TZOFFSETFROM:+000921
TZOFFSETTO:+0000
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19160614T230000
RDATE:19160614T230000
RDATE:19170324T230000
RDATE:19180309T230000
RDATE:19190301T230000
RDATE:19200214T230000
RDATE:19210314T230000
RDATE:19220325T230000
RDATE:19230526T230000
RDATE:19240329T230000
RDATE:19250404T230000
RDATE:19260417T230000
RDATE:19270409T230000
RDATE:19280414T230000
RDATE:19290420T230000
RDATE:19300412T230000
RDATE:19310418T230000
RDATE:19320402T230000
RDATE:19330325T230000
RDATE:19340407T230000
RDATE:19350330T230000
RDATE:19360418T230000
RDATE:19370403T230000
RDATE:19380326T230000
RDATE:19390415T230000
RDATE:19400225T020000
TZNAME:WEST
TZOFFSETFROM:+0000
TZOFFSETTO:+0100
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19161002T000000
RRULE:FREQ=YEARLY;UNTIL=19191005T230000Z;BYMONTH=10;BYMONTHDAY=2,3,4,5,6,
 7,8;BYDAY=MO
TZNAME:WET
TZOFFSETFROM:+0100
TZOFFSETTO:+0000
END:STANDARD
BEGIN:STANDARD
DTSTART:19201024T000000
RDATE:19201024T000000
RDATE:19211026T000000
RDATE:19391119T000000
TZNAME:WET
TZOFFSETFROM:+0100
TZOFFSETTO:+0000
END:STANDARD
BEGIN:STANDARD
DTSTART:19221008T000000
RRULE:FREQ=YEARLY;UNTIL=19381001T230000Z;BYMONTH=10;BYMONTHDAY=2,3,4,5,6,
 7,8;BYDAY=SU
TZNAME:WET
TZOFFSETFROM:+0100
TZOFFSETTO:+0000
END:STANDARD
BEGIN:STANDARD
DTSTART:19400614T230000
RDATE:19400614T230000
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:STANDARD
BEGIN:STANDARD
DTSTART:19421102T030000
RDATE:19421102T030000
RDATE:19431004T030000
RDATE:19760926T010000
RDATE:19770925T030000
RDATE:19781001T030000
TZNAME:CET
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19430329T020000
RDATE:19430329T020000
RDATE:19440403T020000
RDATE:19760328T010000
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19440825T000000
RDATE:19440825T000000
TZNAME:WEST
TZOFFSETFROM:+0200
TZOFFSETTO:+0200
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19441008T010000
RDATE:19441008T010000
TZNAME:WEST
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:DAYLIGHT
BEGIN:DAYLIGHT
DTSTART:19450402T020000
RDATE:19450402T020000
TZNAME:WEMT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19450916T030000
RDATE:19450916T030000
TZNAME:CEST
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:STANDARD
DTSTART:19770101T000000
RDATE:19770101T000000
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19770403T020000
RRULE:FREQ=YEARLY;UNTIL=19800406T010000Z;BYMONTH=4;BYDAY=1SU
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19790930T030000
RRULE:FREQ=YEARLY;UNTIL=19950924T010000Z;BYMONTH=9;BYDAY=-1SU
TZNAME:CET
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19810329T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
TZNAME:CEST
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19961027T030000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
TZNAME:CET
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
END:VTIMEZONE
BEGIN:VTIMEZONE
TZID:US-Eastern
LAST-MODIFIED:19870101T000000Z
TZURL:http://zones.stds_r_us.net/tz/US-Eastern
BEGIN:STANDARD
DTSTART:19671029T020000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10
TZOFFSETFROM:-0400
TZOFFSETTO:-0500
TZNAME:EST
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:19870405T020000
RRULE:FREQ=YEARLY;BYDAY=1SU;BYMONTH=4
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
TZNAME:EDT
END:DAYLIGHT
END:VTIMEZONE
";
  }

  function getIcalFooter() {
    return "END:VCALENDAR
";
  }


}
