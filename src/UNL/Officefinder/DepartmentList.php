<?php
class UNL_Officefinder_DepartmentList extends ArrayIterator implements RecursiveIterator
{
    public $options;

    function current(): mixed
    {
        $current = parent::current();
        if (gettype($current) == 'string') {
            return UNL_Officefinder_Department::getByID($current);
        }

        $dept = new UNL_Officefinder_Department();
        $dept->synchronizeWithArray($current);
        return $dept;
    }

    function hasChildren(): bool
    {
        return $this->current()->hasChildren();
    }

    function getChildren(): null|RecursiveIterator
    {
        return $this->current()->getChildren();
    }
}