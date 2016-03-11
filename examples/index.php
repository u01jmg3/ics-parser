<?php
require_once '../vendor/autoload.php';

use ICal\ICal;

$ical = new ICal('MyCal.ics');
$events = $ical->events();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <title>PHP ICS Parser example</title>
</head>
<body style="background-color: #eee">
<div class="container">
    <h3>PHP ICS Parser example</h3>
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge"><?= $ical->eventCount ?></span>
            The number of events
        </li>
        <li class="list-group-item">
            <span class="badge"><?= $ical->todoCount ?></span>
            The number of todos
        </li>
    </ul>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?= $event->summary . ' (' . date('d-m-Y H:i', $ical->iCalDateToUnixTimestamp($event->dtstart)) . ')' ?></h3>

                    <p>SUMMARY <?= $event->summary ?></p>

                    <p>DTSTART <?= $event->dtstart ?></p>

                    <p>DTEND <?= $event->dtend ?></p>

                    <p>DTSTAMP <?= $event->dtstamp ?></p>

                    <p>UID <?= $event->uid ?></p>

                    <p>CREATED <?= $event->created ?></p>

                    <p>LAST-MODIFIED <?= $event->lastModified ?></p>

                    <p>DESCRIPTION <?= $event->description ?></p>

                    <p>LOCATION <?= $event->location ?></p>

                    <p>SEQUENCE <?= $event->sequence ?></p>

                    <p>STATUS <?= $event->status ?></p>

                    <p>TRANSP <?= $event->transp ?></p>

                    <p>ORGANIZER <?= $event->organizer ?></p>

                    <p>ATTENDEE(S) <?= $event->attendee ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>
</body>
</html>