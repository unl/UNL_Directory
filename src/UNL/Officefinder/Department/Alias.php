<?php
class UNL_Officefinder_Department_Alias extends UNL_Officefinder_Record
{
    public $department_id;
    public $name;

    function getTable()
    {
        return 'department_aliases';
    }

    function keys()
    {
        return array('department_id', 'name');
    }
}