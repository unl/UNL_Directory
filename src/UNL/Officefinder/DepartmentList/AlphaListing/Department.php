<?php
class UNL_Officefinder_DepartmentList_AlphaListing_Department
{
    public $name;

    public $department;

    function __construct($name, $department)
    {
        $this->name = $name;
        $this->department = $department;
    }
}