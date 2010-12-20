<?php
class UNL_Peoplefinder_Person_Roles extends IteratorIterator
{
    function __construct($options = array())
    {
        if (isset($options['iterator'])
            && $options['iterator'] instanceof Iterator) {
            $iterator = $options['iterator'];
        } else {
            if (empty($options['dn'])) {
                throw new Exception('You must supply a base DN from which to search.');
            }
            $iterator = $options['peoplefinder']->getRoles($options['dn']);
        }
        parent::__construct($iterator);
    }
}