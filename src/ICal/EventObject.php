<?php
/**
 * @category    Parser
 * @package     ics-parser
 */

namespace ICal;

class EventObject
{
    public $summary;
    public $dtstart;
    public $dtend;
    public $duration;
    public $dtstamp;
    public $uid;
    public $created;
    public $lastmodified;
    public $description;
    public $location;
    public $sequence;
    public $status;
    public $transp;
    public $organizer;
    public $attendee;

    public function __construct($data = array())
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $variable = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($key)))));
                $this->{$variable} = $value;
            }
        }
    }

    /**
     * Return Event data excluding anything blank
     * within an HTML template
     *
     * @param string $html HTML template to use
     *
     * @return string
     */
    public function printData($html = '<p>%s: %s</p>')
    {
        $data = array(
            'SUMMARY'       => $this->summary,
            'DTSTART'       => $this->dtstart,
            'DTEND'         => $this->dtend,
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