<?php
/**
 * @category Parser
 * @package  ics-parser
 */

namespace ICal;

class EventObject
{
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
     * Creates the Event Object
     *
     * @param  array $data
     * @return void
     */
    public function __construct(array $data = array())
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $variable = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($key)))));
                $this->{$variable} = $value;

                if (is_array($value)) {
                    $this->{$variable} = $value;
                } else {
                    $this->{$variable} = stripslashes(trim(str_replace('\n', "\n", $value)));
                }
            }
        }
    }

    /**
     * Return Event data excluding anything blank
     * within an HTML template
     *
     * @param string $html HTML template to use
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

        $data   = array_map('trim', $data); // Trim all values
        $data   = array_filter($data);      // Remove any blank values
        $output = '';

        foreach ($data as $key => $value) {
            $output .= sprintf($html, $key, $value);
        }

        return $output;
    }
}