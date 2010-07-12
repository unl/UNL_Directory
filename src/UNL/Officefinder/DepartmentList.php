<?php
class UNL_Officefinder_DepartmentList extends ArrayIterator
{
    function current()
    {
        return UNL_Officefinder_Department::getByID(parent::current());
    }
}