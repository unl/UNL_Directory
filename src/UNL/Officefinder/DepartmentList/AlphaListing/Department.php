<?php
class UNL_Officefinder_DepartmentList_AlphaListing_Department
{
    public $options;

    public $name;

    public $department;

    function __construct($name, $department, $options = array())
    {
        $this->name       = $name;
        $this->department = $department;
        $this->options    = $options;
    }
}