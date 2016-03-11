<?php
require_once '../vendor/autoload.php';

use ICal\ICal;
/**
 * This example demonstrates how the Ics-Parser should be used.
 *
 * PHP Version 5
 *
 * @category Example
 * @package  Ics-parser
 * @author   Martin Thoma <info@martin-thoma.de>
 * @license  http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version  SVN: <svn_id>
 * @link     http://code.google.com/p/ics-parser/
 * @example  $ical = new ical('MyCal.ics');
 *           print_r( $ical->events() );
 */
$ical   = new ICal('basic.ics');
$events = $ical->events();

echo 'The number of events: ';
echo $ical->eventCount;
echo "<br />\n";

echo 'The number of todos: ';
echo $ical->todoCount;
echo "<br />\n";
echo '<hr/><hr/>';

foreach ($events as $event) {
    echo 'SUMMARY: ' . $event->summary . "<br />\n";
    echo 'DTSTART: ' . $event->dtstart . "<br />\n";
    echo 'DTEND: ' . $event->dtend . "<br />\n";
    echo 'DTSTAMP: ' . $event->dtstamp . "<br />\n";
    echo 'UID: ' . $event->uid. "<br />\n";
    echo 'CREATED: ' . $event->created . "<br />\n";
    echo 'LAST-MODIFIED: ' . $event->lastModified . "<br />\n";
    echo 'DESCRIPTION: ' . $event->description . "<br />\n";
    echo 'LOCATION: ' . $event->location . "<br />\n";
    echo 'SEQUENCE: ' . $event->sequence . "<br />\n";
    echo 'STATUS: ' . $event->status . "<br />\n";
    echo 'TRANSP: ' . $event->transp . "<br />\n";
    echo 'ORGANIZER: ' . $event->organizer . "<br />\n";
    echo 'ATTENDEE(S): ' . $event->attendee . "<br />\n";
    echo '<hr/>';
}
?>