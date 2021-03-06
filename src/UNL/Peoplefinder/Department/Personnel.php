<?php
class UNL_Peoplefinder_Department_Personnel extends ArrayIterator
{
    public $options = array();

    function __construct($mixed)
    {
        if (is_array($mixed)) {
            if (!empty($mixed['org_unit'])) {
                $this->options = $mixed;
                $mixed = UNL_Peoplefinder::getInstance()->getHROrgUnitNumberMatches($this->options['org_unit']);
            } elseif (isset($mixed['id'])) {
                $this->options = $mixed;
                $department = UNL_Officefinder_Department::getById($mixed['id']);
                $mixed = $department->getHRDepartment()->getLDAPResults();
            }
        }
        parent::__construct($mixed);
    }
}
