<?php
class UNL_Peoplefinder_SearchResults extends ArrayIterator
{
    public $options = array('q'           => '',
                            'affiliation' => '');

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
        } elseif (isset($this->options['sn']) || isset($this->options['cn'])) {
            // Detailed search
            $search_method = 'getAdvancedSearchMatches';
            $this->options['q'] = array(
                'sn' => $this->options['sn'],
                'cn' => $this->options['cn']);
        }

        parent::__construct($this->options['peoplefinder']->$search_method($this->options['q'], $this->options['affiliation']));
    }
}