<?php
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
require 'class.iCalReader.php';

$ical   = new ICal('MyCal.ics');
$events = $ical->events();

$date = $events[0]['DTSTART'];
echo "The ical date: ";
echo $date;
echo "<br/>";

echo "The Unix timestamp: ";
echo $ical->iCalDateToUnixTimestamp($date);
echo "<br/>";

echo "The number of events: ";
echo $ical->event_count;
echo "<br/>";

echo "The number of todos: ";
echo $ical->todo_count;
echo "<br/>";
echo "<hr/><hr/>";

foreach ($events as $event) {
    echo "SUMMARY: ".$event['SUMMARY']."<br/>";
    echo "DTSTART: ".$event['DTSTART']." - UNIX-Time: ".$ical->iCalDateToUnixTimestamp($event['DTSTART'])."<br/>";
    echo "DTEND: ".$event['DTEND']."<br/>";
    echo "DTSTAMP: ".$event['DTSTAMP']."<br/>";
    echo "UID: ".$event['UID']."<br/>";
    echo "CREATED: ".$event['CREATED']."<br/>";
    echo "DESCRIPTION: ".$event['DESCRIPTION']."<br/>";
    echo "LAST-MODIFIED: ".$event['LAST-MODIFIED']."<br/>";
    echo "LOCATION: ".$event['LOCATION']."<br/>";
    echo "SEQUENCE: ".$event['SEQUENCE']."<br/>";
    echo "STATUS: ".$event['STATUS']."<br/>";
    echo "TRANSP: ".$event['TRANSP']."<br/>";
    echo "<hr/>";
}
?>
