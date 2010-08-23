<?php
class UNL_Officefinder_DepartmentList extends ArrayIterator
{
    function current()
    {
        $current = parent::current();
        if (gettype($current) == 'string') {
            return UNL_Officefinder_Department::getByID($current);
        }

        $dept = new UNL_Officefinder_Department();
        $dept->synchronizeWithArray($current);
        return $dept;
    }
}