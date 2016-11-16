<?php
require_once '../vendor/autoload.php';

use ICal\ICal;

$ical = new ICal('MyCal.ics');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <title>PHP ICS Parser example</title>
    <style>.caption { overflow-x: auto }</style>
</head>
<body style="background-color: #eee">
<div class="container">
    <h3>PHP ICS Parser example</h3>
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge"><?php echo $ical->eventCount ?></span>
            The number of events
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $ical->todoCount ?></span>
            The number of todos
        </li>
    </ul>

    <?php
        $events = $ical->eventsFromRange('2016-03-01', '2016-04-31');
        if ($events) echo '<h4>Events March through April:</h4>';
    ?>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?php echo $event->summary . ' (' . date('d-m-Y H:i', $ical->iCalDateToUnixTimestamp($event->dtstart)) . ')' ?></h3>
                    <p>SUMMARY: <?php echo $event->summary ?></p>
                    <p>DTSTART: <?php echo $event->dtstart ?></p>
                    <p>DTEND: <?php echo $event->dtend ?></p>
                    <p>DURATION: <?php echo $event->duration ?></p>
                    <p>DTSTAMP: <?php echo $event->dtstamp ?></p>
                    <p>UID: <?php echo $event->uid ?></p>
                    <p>CREATED: <?php echo $event->created ?></p>
                    <p>LAST-MODIFIED: <?php echo $event->lastmodified ?></p>
                    <p>DESCRIPTION: <?php echo $event->description ?></p>
                    <p>LOCATION: <?php echo $event->location ?></p>
                    <p>SEQUENCE: <?php echo $event->sequence ?></p>
                    <p>STATUS: <?php echo $event->status ?></p>
                    <p>TRANSP: <?php echo $event->transp ?></p>
                    <p>ORGANISER: <?php echo $event->organizer ?></p>
                    <p>ATTENDEE(S): <?php echo $event->attendee ?></p>
                </div>
            </div>
        </div>
    <?php
    endforeach
    ?>
    </div>

    <?php if ($events) echo '<h4>All Events:</h4>' ?>
    <div class="row">
    <?php
    $events = $ical->events();
    foreach ($events as $event) : ?>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?php echo $event->summary . ' (' . date('d-m-Y H:i', $ical->iCalDateToUnixTimestamp($event->dtstart)) . ')' ?></h3>
                    <p>SUMMARY:       <?php echo $event->summary ?></p>
                    <p>DTSTART:       <?php echo $event->dtstart ?></p>
                    <p>DTEND:         <?php echo $event->dtend ?></p>
                    <p>DURATION:      <?php echo $event->duration ?></p>
                    <p>DTSTAMP:       <?php echo $event->dtstamp ?></p>
                    <p>UID:           <?php echo $event->uid ?></p>
                    <p>CREATED:       <?php echo $event->created ?></p>
                    <p>LAST-MODIFIED: <?php echo $event->lastmodified ?></p>
                    <p>DESCRIPTION:   <?php echo $event->description ?></p>
                    <p>LOCATION:      <?php echo $event->location ?></p>
                    <p>SEQUENCE:      <?php echo $event->sequence ?></p>
                    <p>STATUS:        <?php echo $event->status ?></p>
                    <p>TRANSP:        <?php echo $event->transp ?></p>
                    <p>ORGANISER:     <?php echo $event->organizer ?></p>
                    <p>ATTENDEE(S):   <?php echo $event->attendee ?></p>
                </div>
            </div>
        </div>
    <?php
    endforeach
    ?>
    </div>
</div>
</body>
</html>
