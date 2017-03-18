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
<div class="container-fluid">
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
        $events = $ical->eventsFromInterval('1 week');
        if ($events) echo '<h4>Events in the next 7 days:</h4>';
        $count = 1;
    ?>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?php
                        $dtstart = new \DateTime('@' . (int) $ical->iCalDateToUnixTimestamp($event->dtstart, false));
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php if ($count > 1 && $count % 3 === 0) { echo '<div class="clearfix visible-md-block"></div>'; } ?>
        <?php $count++; ?>
    <?php
    endforeach
    ?>
    </div>

    <?php
        $events = $ical->eventsFromRange('2017-03-01 12:00:00', '2017-04-31 17:00:00');
        if ($events) echo '<h4>Events March through April:</h4>';
        $count = 1;
    ?>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?php
                        $dtstart = new \DateTime('@' . (int) $ical->iCalDateToUnixTimestamp($event->dtstart, false));
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php if ($count > 1 && $count % 3 === 0) { echo '<div class="clearfix visible-md-block"></div>'; } ?>
        <?php $count++; ?>
    <?php
    endforeach
    ?>
    </div>

    <?php
        $events = $ical->sortEventsWithOrder($ical->events());
        if ($events) echo '<h4>All Events:</h4>';
    ?>
    <div class="row">
    <?php
    $count = 1;
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?php
                        $dtstart = new \DateTime('@' . (int) $ical->iCalDateToUnixTimestamp($event->dtstart, false));
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php if ($count > 1 && $count % 3 === 0) { echo '<div class="clearfix visible-md-block"></div>'; } ?>
        <?php $count++; ?>
    <?php
    endforeach
    ?>
    </div>
</div>
</body>
</html>