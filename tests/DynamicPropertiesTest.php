<?php

use ICal\ICal;
use PHPUnit\Framework\TestCase;

class DynamicPropertiesTest extends TestCase
{
    // phpcs:disable Squiz.Commenting.FunctionComment

    public function testDynamicArraysAreSet()
    {
        $ical = new ICal('./tests/ical/ical-monthly.ics');

        foreach ($ical->events() as $event) {
            $this->assertTrue(isset($event->dtstart_array));
            $this->assertTrue(isset($event->dtend_array));
            $this->assertTrue(isset($event->dtstamp_array));
            $this->assertTrue(isset($event->uid_array));
            $this->assertTrue(isset($event->created_array));
            $this->assertTrue(isset($event->last_modified_array));
            $this->assertTrue(isset($event->summary_array));
        }
    }
}
