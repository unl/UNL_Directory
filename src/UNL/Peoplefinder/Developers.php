<?php
class UNL_Peoplefinder_Developers
{
    public $resources = array('Record');
    
    public $resource;
    
    public $options = array();
    
    function __construct($options = array()) {
        $this->options  = $options;
        $this->resource = $this->resources[0];
        
        if (isset($this->options['resource']) ) {
            if (in_array($this->options['resource'], $this->resources)) {
                $this->resource = $this->options['resource'];
            }
        }
    }
}