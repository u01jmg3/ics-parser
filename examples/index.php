<?php
// phpcs:disable Generic.Arrays.DisallowLongArraySyntax

require_once '../vendor/autoload.php';

use ICal\ICal;

try {
    $ical = new ICal('ICal.ics', array(
        'defaultSpan'                 => 2,     // Default value
        'defaultTimeZone'             => 'UTC',
        'defaultWeekStart'            => 'MO',  // Default value
        'disableCharacterReplacement' => false, // Default value
        'filterDaysAfter'             => null,  // Default value
        'filterDaysBefore'            => null,  // Default value
        'httpUserAgent'               => null,  // Default value
        'skipRecurrence'              => false, // Default value
    ));
    // $ical->initFile('ICal.ics');
    // $ical->initUrl('https://raw.githubusercontent.com/u01jmg3/ics-parser/master/examples/ICal.ics', $username = null, $password = null, $userAgent = null);
} catch (\Exception $e) {
    die($e);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>PHP ICS Parser example</title>
    <style>body { background-color: #eee }</style>
</head>
<body>
<div class="container-fluid">
    <h4 class="mt-3 mb-2">PHP ICS Parser example</h3>
    <ul class="list-group">
        <li class="list-group-item">
            The number of events
            <span class="badge rounded-pill bg-secondary float-end"><?php echo $ical->eventCount ?></span>
        </li>
        <li class="list-group-item">
            The number of free/busy time slots
            <span class="badge rounded-pill bg-secondary float-end"><?php echo $ical->freeBusyCount ?></span>
        </li>
        <li class="list-group-item">
            The number of todos
            <span class="badge rounded-pill bg-secondary float-end"><?php echo $ical->todoCount ?></span>
        </li>
        <li class="list-group-item">
            The number of alarms
            <span class="badge rounded-pill bg-secondary float-end"><?php echo $ical->alarmCount ?></span>
        </li>
    </ul>

    <?php
        $showExample = array(
            'interval' => true,
            'range'    => true,
            'all'      => true,
        );
    ?>

    <?php
        if ($showExample['interval']) {
            $events = $ical->eventsFromInterval('1 week');

            if ($events) {
                echo '<h4 class="mt-3 mb-2">Events in the next 7 days:</h4>';
            }

            $count = 1;
    ?>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mt-3 mb-2"><?php
                        $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3]);
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php
            if ($count > 1 && $count % 3 === 0) {
                echo '</div><div class="row">';
            }

            $count++;
        ?>
    <?php
    endforeach
    ?>
    </div>
    <?php } ?>

    <?php
        if ($showExample['range']) {
            $events = $ical->eventsFromRange('2017-03-01 12:00:00', '2017-04-31 17:00:00');

            if ($events) {
                echo '<h4 class="mt-3 mb-2">Events March through April:</h4>';
            }

            $count = 1;
    ?>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mt-3 mb-2"><?php
                        $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3]);
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php
            if ($count > 1 && $count % 3 === 0) {
                echo '</div><div class="row">';
            }

            $count++;
        ?>
    <?php
    endforeach
    ?>
    </div>
    <?php } ?>

    <?php
        if ($showExample['all']) {
            $events = $ical->sortEventsWithOrder($ical->events());

            if ($events) {
                echo '<h4 class="mt-3 mb-2">All Events:</h4>';
            }
    ?>
    <div class="row">
    <?php
    $count = 1;
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mt-3 mb-2"><?php
                        $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3]);
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php
            if ($count > 1 && $count % 3 === 0) {
                echo '</div><div class="row">';
            }

            $count++;
        ?>
    <?php
    endforeach
    ?>
    </div>
    <?php } ?>
</div>
</body>
</html>
