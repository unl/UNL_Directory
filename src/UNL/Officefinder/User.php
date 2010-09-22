<?php
class UNL_Officefinder_User
{
    public $uid;

    function __construct($uid)
    {
        $this->uid = $uid;
    }

    function __toString()
    {
        return $this->uid;
    }

    function getDepartments()
    {
        return new UNL_Officefinder_User_Departments(array('uid'=>$this->uid));
    }
}