<?php
/**
 * @category Parser
 * @package  ics-parser
 * @author Tanya Kalashnik <tanchik194@bk.ru>
 */

namespace ICal;

class EventObject
{
    public $summary;
    public $dtstart;
    public $dtend;
    public $dtstamp;
    public $uid;
    public $created;
    public $lastModified;
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

    public function dateToUnixTimestamp($date)
    {

    }
}
