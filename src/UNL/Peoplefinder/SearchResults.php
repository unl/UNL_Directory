<?php
class UNL_Peoplefinder_SearchResults extends ArrayIterator
{
    public $options = array('affiliation' => '',
                            'results'     => array());

    function __construct($options = array())
    {
        $this->options = $options + $this->options;

        parent::__construct($this->options['results']);
    }
}