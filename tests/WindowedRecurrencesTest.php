<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;

class WindowedRecurrencesTest extends TestCase
{
    /**
     * @dataProvider movedOutsideWindowProvider
     */
    public function testMovedOccurrenceOutsideWindowDoesNotRestoreOriginal($movedStart, $recurrenceId, $overrideFirst)
    {
        $ical = $this->parseMovedOccurrence($movedStart, $recurrenceId, $overrideFirst, true);
        $starts = array_map(function ($event) {
            return $event->dtstart;
        }, $ical->events());
        sort($starts);

        $expected = array('20240910T090000', '20240911T090000', '20240912T090000');
        $expected = array_values(array_diff($expected, array($recurrenceId)));

        $this->assertSame($expected, $starts);
        $this->assertSame(2, $ical->eventCount);
        foreach ($ical->events() as $event) {
            $this->assertSame('Original', $event->summary);
        }
    }

    public function movedOutsideWindowProvider()
    {
        return array(
            'first moved earlier, master first' => array('20240901T090000', '20240910T090000', false),
            'first moved earlier, override first' => array('20240901T090000', '20240910T090000', true),
            'first moved later, master first' => array('20240920T090000', '20240910T090000', false),
            'first moved later, override first' => array('20240920T090000', '20240910T090000', true),
            'later moved earlier, master first' => array('20240901T090000', '20240911T090000', false),
            'later moved earlier, override first' => array('20240901T090000', '20240911T090000', true),
            'later moved later, master first' => array('20240920T090000', '20240911T090000', false),
            'later moved later, override first' => array('20240920T090000', '20240911T090000', true),
        );
    }

    public function testMovedOccurrenceRemainsVisibleWithoutWindow()
    {
        $ical = $this->parseMovedOccurrence('20240920T090000', '20240911T090000', false, false);
        $starts = array_map(function ($event) {
            return $event->dtstart;
        }, $ical->events());
        sort($starts);

        $this->assertSame(array('20240910T090000', '20240912T090000', '20240920T090000'), $starts);
        $this->assertSame(3, $ical->eventCount);
    }

    public function testMovedOccurrenceInsideWindowRemainsVisible()
    {
        $ical = $this->parseMovedOccurrence('20240913T090000', '20240911T090000', true, true);
        $starts = array_map(function ($event) {
            return $event->dtstart;
        }, $ical->events());
        sort($starts);

        $this->assertSame(array('20240910T090000', '20240912T090000', '20240913T090000'), $starts);
        $this->assertSame(3, $ical->eventCount);
    }

    private function parseMovedOccurrence($movedStart, $recurrenceId, $overrideFirst, $filter)
    {
        $master = implode(PHP_EOL, array(
            'BEGIN:VEVENT',
            'UID:window-override',
            'DTSTART;TZID=Europe/Paris:20240910T090000',
            'DTEND;TZID=Europe/Paris:20240910T100000',
            'RRULE:FREQ=DAILY;COUNT=3',
            'SUMMARY:Original',
            'END:VEVENT',
        ));
        $override = implode(PHP_EOL, array(
            'BEGIN:VEVENT',
            'UID:window-override',
            'DTSTART;TZID=Europe/Paris:' . $movedStart,
            'DURATION:PT1H',
            'RECURRENCE-ID;TZID=Europe/Paris:' . $recurrenceId,
            'SUMMARY:Moved',
            'END:VEVENT',
        ));
        $options = array('defaultTimeZone' => 'Europe/Paris');
        if ($filter) {
            $options['filterDaysBefore'] = new DateTime('2024-09-09T00:00:00Z');
            $options['filterDaysAfter'] = new DateTime('2024-09-15T23:59:59Z');
        }

        $ical = new ICal(false, $options);
        $ical->initString(implode(PHP_EOL, array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            $overrideFirst ? $override : $master,
            $overrideFirst ? $master : $override,
            'END:VCALENDAR',
        )));

        return $ical;
    }
}
