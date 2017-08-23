<?php

use ICal\ICal;

class ICalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Confirms that the library can be initialized by passing in an array
     * of parsed ICalendar data
     */
    public function testInitializeFromArrayOfLines()
    {
        $lines = [
            [
                'BEGIN:VCALENDAR',
                'END:VCALENDAR'
            ]
        ];

        $ical = new ICal($lines);
    }

}