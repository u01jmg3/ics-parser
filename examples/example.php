<?php
require_once '../src/ICal/ICal.php';

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
 *           print_r( $ical->get_event_array() );
 */
$ical   = new ICal('basic2.ics');
$events = $ical->events();

$date = reset($events)['DTSTART'];
echo 'The ical date: ';
echo $date;
echo "<br />\n";

echo 'The Unix timestamp: ';
echo $ical->iCalDateToUnixTimestamp($date);
echo "<br />\n";

echo 'The number of events: ';
echo $ical->event_count;
echo "<br />\n";

echo 'The number of todos: ';
echo $ical->todo_count;
echo "<br />\n";
echo '<hr/><hr/>';

foreach ($events as $event) {
    echo 'SUMMARY: ' . @$event['SUMMARY'] . "<br />\n";
    echo 'DTSTART: ' . $event['DTSTART'] . ' - UNIX-Time: ' . $ical->iCalDateToUnixTimestamp($event['DTSTART']) . "<br />\n";
    echo 'DTEND: ' . $event['DTEND'] . "<br />\n";
    echo 'DTSTAMP: ' . $event['DTSTAMP'] . "<br />\n";
    echo 'UID: ' . $event['UID'] . "<br />\n";
    echo 'CREATED: ' . @$event['CREATED'] . "<br />\n";
    echo 'LAST-MODIFIED: ' . @$event['LAST-MODIFIED'] . "<br />\n";
    echo 'DESCRIPTION: ' . @$event['DESCRIPTION'] . "<br />\n";
    echo 'LOCATION: ' . @$event['LOCATION'] . "<br />\n";
    echo 'SEQUENCE: ' . @$event['SEQUENCE'] . "<br />\n";
    echo 'STATUS: ' . @$event['STATUS'] . "<br />\n";
    echo 'TRANSP: ' . @$event['TRANSP'] . "<br />\n";
    echo 'ORGANIZER: ' . @$event['ORGANIZER'] . "<br />\n";
    echo 'ATTENDEE(S): ' . @$event['ATTENDEE'] . "<br />\n";
    echo '<hr/>';
}
?>