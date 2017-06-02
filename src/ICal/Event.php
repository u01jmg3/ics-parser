<?php
/**
 * @category Parser
 * @package  ics-parser
 */

namespace ICal;

class Event
{
    const TIMEZONE_TEMPLATE = 'TZID=%s:';

    /**
     * http://www.kanzaki.com/docs/ical/summary.html
     *
     * @var $summary
     */
    public $summary;

    /**
     * http://www.kanzaki.com/docs/ical/dtstart.html
     *
     * @var $dtstart
     */
    public $dtstart;

    /**
     * http://www.kanzaki.com/docs/ical/dtend.html
     *
     * @var $dtend
     */
    public $dtend;

    /**
     * http://www.kanzaki.com/docs/ical/duration.html
     *
     * @var $duration
     */
    public $duration;

    /**
     * http://www.kanzaki.com/docs/ical/dtstamp.html
     *
     * @var $dtstamp
     */
    public $dtstamp;

    /**
     * http://www.kanzaki.com/docs/ical/uid.html
     *
     * @var $uid
     */
    public $uid;

    /**
     * http://www.kanzaki.com/docs/ical/created.html
     *
     * @var $created
     */
    public $created;

    /**
     * http://www.kanzaki.com/docs/ical/lastModified.html
     *
     * @var $lastmodified
     */
    public $lastmodified;

    /**
     * http://www.kanzaki.com/docs/ical/description.html
     *
     * @var $description
     */
    public $description;

    /**
     * http://www.kanzaki.com/docs/ical/location.html
     *
     * @var $location
     */
    public $location;

    /**
     * http://www.kanzaki.com/docs/ical/sequence.html
     *
     * @var $sequence
     */
    public $sequence;

    /**
     * http://www.kanzaki.com/docs/ical/status.html
     *
     * @var $status
     */
    public $status;

    /**
     * http://www.kanzaki.com/docs/ical/transp.html
     *
     * @var $transp
     */
    public $transp;

    /**
     * http://www.kanzaki.com/docs/ical/organizer.html
     *
     * @var $organizer
     */
    public $organizer;

    /**
     * http://www.kanzaki.com/docs/ical/attendee.html
     *
     * @var $attendee
     */
    public $attendee;

    /**
     * The ICal instance
     *
     * @var ICal
     */
    public $ical;

    /**
     * Creates the Event object
     *
     * @param  ICal  $ical
     * @param  array $data
     * @return void
     */
    public function __construct($ical, array $data = array())
    {
        $this->ical = $ical;

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $variable = self::snakeCase($key);
                $this->{$variable} = self::prepareData($value);
            }

            $this->updateEventTimeZoneString();
        }
    }

    /**
     * Prepares the data for output
     *
     * @param  mixed $value
     * @return mixed
     */
    protected function prepareData($value)
    {
        if (is_string($value)) {
            return stripslashes(trim(str_replace('\n', "\n", $value)));
        } else if (is_array($value)) {
            return array_map('self::prepareData', $value);
        }

        return $value;
    }

    /**
     * Return Event data excluding anything blank
     * within an HTML template
     *
     * @param  string $html HTML template to use
     * @return string
     */
    public function printData($html = '<p>%s: %s</p>')
    {
        $data = array(
            'SUMMARY'       => $this->summary,
            'DTSTART'       => $this->dtstart,
            'DTEND'         => $this->dtend,
            'DTSTART_TZ'    => $this->dtstart_tz,
            'DTEND_TZ'      => $this->dtend_tz,
            'DURATION'      => $this->duration,
            'DTSTAMP'       => $this->dtstamp,
            'UID'           => $this->uid,
            'CREATED'       => $this->created,
            'LAST-MODIFIED' => $this->lastmodified,
            'DESCRIPTION'   => $this->description,
            'LOCATION'      => $this->location,
            'SEQUENCE'      => $this->sequence,
            'STATUS'        => $this->status,
            'TRANSP'        => $this->transp,
            'ORGANISER'     => $this->organizer,
            'ATTENDEE(S)'   => $this->attendee,
        );

        $data   = array_filter($data); // Remove any blank values
        $output = '';

        foreach ($data as $key => $value) {
            $output .= sprintf($html, $key, $value);
        }

        return $output;
    }

    /**
     * Convert the given input to snake_case
     *
     * @param  string $input
     * @param  string $glue
     * @param  string $separator
     * @return string
     */
    protected static function snakeCase($input, $glue = '_', $separator = '-')
    {
        $input = preg_split('/(?<=[a-z])(?=[A-Z])/x', $input);
        $input = join($input, $glue);
        $input = str_replace($separator, $glue, $input);

        return strtolower($input);
    }

    /**
     * Extend `{DTSTART|DTEND|RECURRENCE-ID}_array` to include
     * `TZID=Timezone:YYYYMMDD[T]HHMMSS` of each event
     *
     * @return void
     */
    protected function updateEventTimeZoneString()
    {
        $eventTimeZoneStringIndex = 3;
        $calendarTimeZone = $this->ical->calendarTimeZone(true);

        $dtStartTimeZone = (isset($this->dtstart_array[0]['TZID'])) ? $this->dtstart_array[0]['TZID'] : $calendarTimeZone;
        $this->dtstart_array[$eventTimeZoneStringIndex] = ((is_null($dtStartTimeZone)) ? '' : sprintf(self::TIMEZONE_TEMPLATE, $dtStartTimeZone)) . $this->dtstart_array[1];

        if (isset($this->dtend_array)) {
            $dtEndTimeZone = (isset($this->dtend_array[0]['TZID'])) ? $this->dtend_array[0]['TZID'] : $calendarTimeZone;
            $this->dtend_array[$eventTimeZoneStringIndex] = ((is_null($dtEndTimeZone)) ? '' : sprintf(self::TIMEZONE_TEMPLATE, $dtEndTimeZone)) . $this->dtend_array[1];
        }

        if (isset($this->recurrence_id_array)) {
            $recurrenceIdTimeZone = (isset($this->recurrence_id_array[0]['TZID'])) ? $this->recurrence_id_array[0]['TZID'] : $calendarTimeZone;
            $this->recurrence_id_array[$eventTimeZoneStringIndex] = ((is_null($recurrenceIdTimeZone)) ? '' : sprintf(self::TIMEZONE_TEMPLATE, $recurrenceIdTimeZone)) . $this->recurrence_id_array[1];
        }
    }
}