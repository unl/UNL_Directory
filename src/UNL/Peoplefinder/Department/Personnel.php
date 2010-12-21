<?php
class UNL_Peoplefinder_Department_Personnel extends ArrayIterator
{
    public $options = array();

    function __construct($mixed)
    {
        if (is_array($mixed)
            && !empty($mixed['org_unit'])) {
            $this->options = $mixed;
            $peoplefinder  = new UNL_Peoplefinder(array('driver'=>$this->options['driver']));
            $mixed = $peoplefinder->getHROrgUnitNumberMatches($this->options['org_unit']);
        }
        parent::__construct($mixed);
    }
}