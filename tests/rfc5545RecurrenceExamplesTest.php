<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;

/**
 * The following tests are based on the event recurrence examples given
 * in the RFC5545 iCal specification (https://tools.ietf.org/html/rfc5545,
 * pages 123 to 132).
 *
 * Whilst this might not catch edge-cases, it does give a basic set of
 * rudimentary tests which have established, published, results. They
 * also serve as a guide to the level of implementation.
 *
 * Infinite Recurrence:
 *   There are certain tests below that, as given as examples in RFC5545,
 *   recur forever. Our ics-parser handles these by calculating all
 *   recurrences from the start date until the current date plus a
 *   user-definable number of years (default: 2).
 *
 *   Whilst this is fine for normal use, for the purposes of testing
 *   (where one of the things we're checking is how many events the rule
 *   generates) this presents a problem as the date upon which the test
 *   is run changes the determined stop date, and thus the number of
 *   events the parser ultimately generates by following the RRULE.
 *
 *   To get round this, limits have been added to all "endless" rrules,
 *   so as to give a finite number of occurrences. All tests where such
 *   a limit has been added are clearly marked as such below.
 *
 * Non-implemented RRULE parts:
 *   At the time of writing this file, our parser does not implement the
 *   full range of RRULE parts as described in the standard. Tests that
 *   use the non-implemented parts have been added below, but have been
 *   left commented out, annotated with the applicable ticket number.
 *
 *   (Hint: search string `[#` or `[No ticket]`)
 *
 *   Once support for certain RRULE parts have been implemented, the
 *   relevant test(s) below can be uncommented.
 *
 * Further issues:
 *   There are some other parser issues which cause test failure, which
 *   have been similarly commented out and annotated with a ticket number
 *   (if one exists).
 */
class Rfc5545Examples extends TestCase
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

    // Page 123, Test 1 :: Daily, 10 Occurences
    public function test_page123_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970903T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970904T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;COUNT=10',
            ),
            10,
            $checks
        );
    }

    // Page 123, Test 2 :: Daily, until December 24th
    public function test_page123_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970903T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970904T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;UNTIL=19971224T000000Z',
            ),
            113,
            $checks
        );
    }

    // Page 124, Test 1 :: Daily, every other day, Forever
    //
    // UNTIL rule does not exist in original example
    public function test_page124_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970904T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970906T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;INTERVAL=2;UNTIL=19971201Z',
            ),
            45,
            $checks
        );
    }

    // Page 124, Test 2 :: Daily, 10-day intervals, 5 occurrences
    public function test_page124_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970912T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970922T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;INTERVAL=10;COUNT=5',
            ),
            5,
            $checks
        );
    }

    // Page 124, Test 3a :: Every January day, for 3 years (Variant A)
    public function test_page124_test3a()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19980101T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19980102T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19980103T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19980101T090000',
                'RRULE:FREQ=YEARLY;UNTIL=20000131T140000Z;BYMONTH=1;BYDAY=SU,MO,TU,WE,TH,FR,SA',
            ),
            93,
            $checks
        );
    }

/*  Requires support for BYMONTH under DAILY [No ticket]
 *
    // Page 124, Test 3b :: Every January day, for 3 years (Variant B)
    public function test_page124_test3b()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19980101T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19980102T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19980103T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19980101T090000',
                'RRULE:FREQ=DAILY;UNTIL=20000131T140000Z;BYMONTH=1',
            ),
            93,
            $checks
        );
    }
*/

    // Page 124, Test 4 :: Weekly, 10 occurrences
    public function test_page124_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970909T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970916T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;COUNT=10',
            ),
            10,
            $checks
        );
    }

    // Page 125, Test 1 :: Weekly, until December 24th
    public function test_page125_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970909T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970916T090000', 'message' => '3rd occurrence: '),
            array('index' => 16, 'dateString' => '19971223T090000', 'message' => 'last occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;UNTIL=19971224T000000Z',
            ),
            17,
            $checks
        );
    }

    // Page 125, Test 2 :: Every other week, forever
    //
    // UNTIL rule does not exist in original example
    public function test_page125_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970916T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970930T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971014T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19971028T090000', 'message' => '5th occurrence: '),
            array('index' => 5, 'dateString' => '19971111T090000', 'message' => '6th occurrence: '),
            array('index' => 6, 'dateString' => '19971125T090000', 'message' => '7th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;WKST=SU;UNTIL=19971201Z',
            ),
            7,
            $checks
        );
    }

    // Page 125, Test 3a :: Tuesday & Thursday every week, for five weeks (Variant A)
    public function test_page125_test3a()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970904T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970909T090000', 'message' => '3rd occurrence: '),
            array('index' => 9, 'dateString' => '19971002T090000', 'message' => 'final occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;UNTIL=19971007T000000Z;WKST=SU;BYDAY=TU,TH',
            ),
            10,
            $checks
        );
    }

    // Page 125, Test 3b :: Tuesday & Thursday every week, for five weeks (Variant B)
    public function test_page125_test3b()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970904T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970909T090000', 'message' => '3rd occurrence: '),
            array('index' => 9, 'dateString' => '19971002T090000', 'message' => 'final occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;COUNT=10;WKST=SU;BYDAY=TU,TH',
            ),
            10,
            $checks
        );
    }

    // Page 125, Test 4 :: Monday, Wednesday & Friday of every other week until December 24th
    public function test_page125_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970901T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970903T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970905T090000', 'message' => '3rd occurrence: '),
            array('index' => 24, 'dateString' => '19971222T090000', 'message' => 'final occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970901T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;UNTIL=19971224T000000Z;WKST=SU;BYDAY=MO,WE,FR',
            ),
            25,
            $checks
        );
    }

    // Page 126, Test 1 :: Tuesday & Thursday, every other week, for 8 occurrences
    public function test_page126_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970904T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970916T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;COUNT=8;WKST=SU;BYDAY=TU,TH',
            ),
            8,
            $checks
        );
    }

    // Page 126, Test 2 :: First Friday of the Month, for 10 occurrences
    public function test_page126_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970905T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971003T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971107T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970905T090000',
                'RRULE:FREQ=MONTHLY;COUNT=10;BYDAY=1FR',
            ),
            10,
            $checks
        );
    }

    // Page 126, Test 3 :: First Friday of the Month, until 24th December
    public function test_page126_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970905T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971003T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971107T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970905T090000',
                'RRULE:FREQ=MONTHLY;UNTIL=19971224T000000Z;BYDAY=1FR',
            ),
            4,
            $checks
        );
    }

    // Page 126, Test 4 :: First and last Sunday, every other Month, for 10 occurrences
    public function test_page126_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970907T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970928T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971102T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971130T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19980104T090000', 'message' => '5th occurrence: '),
            array('index' => 5, 'dateString' => '19980125T090000', 'message' => '6th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970907T090000',
                'RRULE:FREQ=MONTHLY;INTERVAL=2;COUNT=10;BYDAY=1SU,-1SU',
            ),
            10,
            $checks
        );
    }

    // Page 126, Test 5 :: Second-to-last Monday of the Month, for six months
    public function test_page126_test5()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970922T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971020T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971117T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970922T090000',
                'RRULE:FREQ=MONTHLY;COUNT=6;BYDAY=-2MO',
            ),
            6,
            $checks
        );
    }

    // Page 127, Test 1 :: Third-to-last day of the month, forever
    //
    // UNTIL rule does not exist in original example.
    public function test_page127_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970928T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971029T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971128T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971229T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19980129T090000', 'message' => '5th occurrence: '),
            array('index' => 5, 'dateString' => '19980226T090000', 'message' => '6th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970928T090000',
                'RRULE:FREQ=MONTHLY;BYMONTHDAY=-3;UNTIL=19980401',
            ),
            7,
            $checks
        );
    }

    // Page 127, Test 2 :: 2nd and 15th of each Month, for 10 occurrences
    public function test_page127_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970915T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971002T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971015T090000', 'message' => '4th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=MONTHLY;COUNT=10;BYMONTHDAY=2,15',
            ),
            10,
            $checks
        );
    }

    // Page 127, Test 3 :: First and last day of the month, for 10 occurrences
    public function test_page127_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970930T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971001T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971031T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971101T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19971130T090000', 'message' => '5th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970930T090000',
                'RRULE:FREQ=MONTHLY;COUNT=10;BYMONTHDAY=1,-1',
            ),
            10,
            $checks
        );
    }

    // Page 127, Test 4 :: 10th through 15th, every 18 months, for 10 occurrences
    public function test_page127_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970910T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970911T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970912T090000', 'message' => '3rd occurrence: '),
            array('index' => 6, 'dateString' => '19990310T090000', 'message' => '7th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970910T090000',
                'RRULE:FREQ=MONTHLY;INTERVAL=18;COUNT=10;BYMONTHDAY=10,11,12,13,14,15',
            ),
            10,
            $checks
        );
    }

    // Page 127, Test 5 :: Every Tuesday, every other Month, forever
    //
    // UNTIL rule does not exist in original example.
    public function test_page127_test5()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970909T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970916T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=MONTHLY;INTERVAL=2;BYDAY=TU;UNTIL=19980101',
            ),
            9,
            $checks
        );
    }

    // Page 128, Test 1 :: June & July of each Year, for 10 occurrences
    public function test_page128_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970610T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970710T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19980610T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970610T090000',
                'RRULE:FREQ=YEARLY;COUNT=10;BYMONTH=6,7',
            ),
            10,
            $checks
        );
    }

    // Page 128, Test 2 :: January, February, & March, every other Year, for 10 occurrences
    public function test_page128_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970310T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19990110T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19990210T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970310T090000',
                'RRULE:FREQ=YEARLY;INTERVAL=2;COUNT=10;BYMONTH=1,2,3',
            ),
            10,
            $checks
        );
    }

    // Page 128, Test 3 :: Every third Year on the 1st, 100th, & 200th day for 10 occurrences
    public function test_page128_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970101T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970410T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970719T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970101T090000',
                'RRULE:FREQ=YEARLY;INTERVAL=3;COUNT=10;BYYEARDAY=1,100,200',
            ),
            10,
            $checks
        );
    }

    // Page 128, Test 4 :: 20th Monday of a Year, forever
    //
    // COUNT rule does not exist in original example.
    public function test_page128_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970519T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19980518T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19990517T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970519T090000',
                'RRULE:FREQ=YEARLY;BYDAY=20MO;COUNT=4',
            ),
            4,
            $checks
        );
    }

    // Page 129, Test 1 :: Monday of Week 20, where the default start of the week is Monday, forever
    //
    // COUNT rule does not exist in original example.
    public function test_page129_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970512T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19980511T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19990517T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970512T090000',
                'RRULE:FREQ=YEARLY;BYWEEKNO=20;BYDAY=MO;COUNT=4',
            ),
            4,
            $checks
        );
    }

    // Page 129, Test 2 :: Every Thursday in March, forever
    //
    // UNTIL rule does not exist in original example.
    public function test_page129_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970313T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970320T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970327T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970313T090000',
                'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=TH;UNTIL=19990401Z',
            ),
            11,
            $checks
        );
    }

    // Page 129, Test 3 :: Every Thursday in June, July, & August, forever
    //
    // UNTIL rule does not exist in original example.
    public function test_page129_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970605T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970612T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970619T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970605T090000',
                'RRULE:FREQ=YEARLY;BYDAY=TH;BYMONTH=6,7,8;UNTIL=19970901Z',
            ),
            13,
            $checks
        );
    }

/*  Requires support for BYMONTHDAY and BYDAY in the same MONTHLY RRULE [No ticket]
 *
    // Page 129, Test 4 :: Every Friday 13th, forever
    //
    // COUNT rule does not exist in original example.
    public function test_page129_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19980213T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19980313T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19981113T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19990813T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '20001013T090000', 'message' => '5th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'EXDATE;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=MONTHLY;BYDAY=FR;BYMONTHDAY=13;COUNT=5',
            ),
            5,
            $checks
        );
    }
*/

    // Page 130, Test 1 :: The first Saturday that follows the first Sunday of the month, forever:
    //
    // COUNT rule does not exist in original example.
    public function test_page130_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970913T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971011T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971108T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970913T090000',
                'RRULE:FREQ=MONTHLY;BYDAY=SA;BYMONTHDAY=7,8,9,10,11,12,13;COUNT=7',
            ),
            7,
            $checks
        );
    }

    // Page 130, Test 2 :: The first Tuesday after a Monday in November, every 4 Years (U.S. Presidential Election Day), forever
    //
    // COUNT rule does not exist in original example.
    public function test_page130_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19961105T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '20001107T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '20041102T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19961105T090000',
                'RRULE:FREQ=YEARLY;INTERVAL=4;BYMONTH=11;BYDAY=TU;BYMONTHDAY=2,3,4,5,6,7,8;COUNT=4',
            ),
            4,
            $checks
        );
    }

    // Page 130, Test 3 :: Third instance of either a Tuesday, Wednesday, or Thursday of a Month, for 3 months.
    public function test_page130_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970904T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971007T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971106T090000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970904T090000',
                'RRULE:FREQ=MONTHLY;COUNT=3;BYDAY=TU,WE,TH;BYSETPOS=3',
            ),
            3,
            $checks
        );
    }

    // Page 130, Test 4 :: Second-to-last weekday of the month, indefinitely
    //
    // UNTIL rule does not exist in original example.
    public function test_page130_test4()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970929T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19971030T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19971127T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19971230T090000', 'message' => '4th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970929T090000',
                'RRULE:FREQ=MONTHLY;BYDAY=MO,TU,WE,TH,FR;BYSETPOS=-2;UNTIL=19980101',
            ),
            4,
            $checks
        );
    }

/*  Requires support of HOURLY frequency [#101]
 *
    // Page 131, Test 1 :: Every 3 hours from 09:00 to 17:00 on a specific day
    public function test_page131_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970902T120000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970902T150000', 'message' => '3rd occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'FREQ=HOURLY;INTERVAL=3;UNTIL=19970902T170000Z',
            ),
            3,
            $checks
        );
    }
*/

/*  Requires support of MINUTELY frequency [#101]
 *
    // Page 131, Test 2 :: Every 15 minutes for 6 occurrences
    public function test_page131_test2()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970902T091500', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970902T093000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19970902T094500', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '19970902T100000', 'message' => '5th occurrence: '),
            array('index' => 5, 'dateString' => '19970902T101500', 'message' => '6th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=MINUTELY;INTERVAL=15;COUNT=6',
            ),
            6,
            $checks
        );
    }
*/

/*  Requires support of MINUTELY frequency [#101]
 *
    // Page 131, Test 3 :: Every hour and a half for 4 occurrences
    public function test_page131_test3()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970902T103000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970902T120000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19970902T133000', 'message' => '4th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=MINUTELY;INTERVAL=90;COUNT=4',
            ),
            4,
            $checks
        );
    }
*/

/*  Requires support of BYHOUR and BYMINUTE under DAILY [#11]
 *
    // Page 131, Test 4a :: Every 20 minutes from 9:00 to 16:40 every day, using DAILY
    //
    // UNTIL rule does not exist in original example
    public function test_page131_test4a()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence, Day 1: '),
            array('index' => 1, 'dateString' => '19970902T092000', 'message' => '2nd occurrence, Day 1: '),
            array('index' => 2, 'dateString' => '19970902T094000', 'message' => '3rd occurrence, Day 1: '),
            array('index' => 3, 'dateString' => '19970902T100000', 'message' => '4th occurrence, Day 1: '),
            array('index' => 20, 'dateString' => '19970902T164000', 'message' => 'Last occurrence, Day 1: '),
            array('index' => 21, 'dateString' => '19970903T090000', 'message' => '1st occurrence, Day 2: '),
            array('index' => 41, 'dateString' => '19970903T164000', 'message' => 'Last occurrence, Day 2: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=DAILY;BYHOUR=9,10,11,12,13,14,15,16;BYMINUTE=0,20,40;UNTIL=19970904T000000Z',
            ),
            42,
            $checks
        );
    }
*/

/*  Requires support of MINUTELY frequency [#101]
 *
    // Page 131, Test 4b :: Every 20 minutes from 9:00 to 16:40 every day, using MINUTELY
    //
    // UNTIL rule does not exist in original example
    public function test_page131_test4b()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970902T090000', 'message' => '1st occurrence, Day 1: '),
            array('index' => 1, 'dateString' => '19970902T092000', 'message' => '2nd occurrence, Day 1: '),
            array('index' => 2, 'dateString' => '19970902T094000', 'message' => '3rd occurrence, Day 1: '),
            array('index' => 3, 'dateString' => '19970902T100000', 'message' => '4th occurrence, Day 1: '),
            array('index' => 20, 'dateString' => '19970902T164000', 'message' => 'Last occurrence, Day 1: '),
            array('index' => 21, 'dateString' => '19970903T090000', 'message' => '1st occurrence, Day 2: '),
            array('index' => 41, 'dateString' => '19970903T164000', 'message' => 'Last occurrence, Day 2: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970902T090000',
                'RRULE:FREQ=MINUTELY;INTERVAL=20;BYHOUR=9,10,11,12,13,14,15,16;UNTIL=19970904T000000Z',
            ),
            42,
            $checks
        );
    }
*/

    // Page 131, Test 5a :: Changing the passed WKST rule, before...
    public function test_page131_test5a()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970805T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970810T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970819T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19970824T090000', 'message' => '4th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970805T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;COUNT=4;BYDAY=TU,SU;WKST=MO',
            ),
            4,
            $checks
        );
    }

    // Page 131, Test 5b :: ...and after
    public function test_page131_test5b()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '19970805T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '19970817T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '19970819T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '19970831T090000', 'message' => '4th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:19970805T090000',
                'RRULE:FREQ=WEEKLY;INTERVAL=2;COUNT=4;BYDAY=TU,SU;WKST=SU',
            ),
            4,
            $checks
        );
    }

    // Page 132, Test 1 :: Automatically ignoring an invalid date (30 February)
    public function test_page132_test1()
    {
        $checks = array(
            array('index' => 0, 'dateString' => '20070115T090000', 'message' => '1st occurrence: '),
            array('index' => 1, 'dateString' => '20070130T090000', 'message' => '2nd occurrence: '),
            array('index' => 2, 'dateString' => '20070215T090000', 'message' => '3rd occurrence: '),
            array('index' => 3, 'dateString' => '20070315T090000', 'message' => '4th occurrence: '),
            array('index' => 4, 'dateString' => '20070330T090000', 'message' => '5th occurrence: '),
        );
        $this->assertVEVENT(
            'America/New_York',
            array(
                'DTSTART;TZID=America/New_York:20070115T090000',
                'RRULE:FREQ=MONTHLY;BYMONTHDAY=15,30;COUNT=5',
            ),
            5,
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
            'defaultSpan'                 => 2,                // Default value: 2
            'defaultTimeZone'             => $defaultTimezone, // Default value: UTC
            'defaultWeekStart'            => 'MO',             // Default value
            'disableCharacterReplacement' => false,            // Default value
            'filterDaysAfter'             => null,             // Default value
            'filterDaysBefore'            => null,             // Default value
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
                'UID:RFC5545-examples-test',
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
