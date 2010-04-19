<?php
class UNL_Peoplefinder_SearchResults extends ArrayIterator
{
    public $options = array('q' => '');

    function __construct($options = array())
    {
        $this->options = $options + $this->options;

        if (strlen($this->options['q']) <= 3) {
            throw new Exception('Please enter more information');
        }

        $search_method = 'getExactMatches';

        if (is_numeric(str_replace(array('-', '(', ')'),
                                   array('',  '',  ''),
                                   $this->options['q']))) {
            // Phone number search
            $search_method = 'getPhoneMatches';
        }

        parent::__construct($this->options['peoplefinder']->$search_method($this->options['q']));
    }
}