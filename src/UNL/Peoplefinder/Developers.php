<?php
class UNL_Peoplefinder_Developers
{
    public $resources = [
        'Record' => 'Person Record',
        'Search' => 'Search',
        'Department' => 'Department Record',
        'Department_Personnel' => 'Department Personnel',
    ];

    public $resource;

    public $options = [];

    function __construct($options = [])
    {
        $this->options  = $options;
        $this->resource = key($this->resources);

        if (isset($this->options['resource']) ) {
            if (array_key_exists($this->options['resource'], $this->resources)) {
                $this->resource = $this->options['resource'];
            }
        }
    }
}
