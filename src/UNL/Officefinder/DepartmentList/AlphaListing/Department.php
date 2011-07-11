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

    function __call($method, $params)
    {
        return call_user_func_array(array($this->department, $method), $params);
    }

    function __get($var)
    {
        return $this->department->$var;
    }
}