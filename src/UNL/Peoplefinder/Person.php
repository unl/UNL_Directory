<?php

class UNL_Peoplefinder_Person
{
    public $record;

    public $knowledge;

    public $options = [];

    function __construct($options = array())
    {
        if (isset($options['uid'])
            && $options['peoplefinder']) {
            $this->record = $options['peoplefinder']->getUID($options['uid']);
        }

        if (isset($options['uid'])) {
            $this->knowledge = new UNL_Knowledge();
            $this->knowledge = $this->knowledge->getRecords($options['uid']);
        }

        $this->options = $options;
    }
}
