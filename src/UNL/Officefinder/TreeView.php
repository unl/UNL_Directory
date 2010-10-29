<?php
class UNL_Officefinder_TreeView extends RecursiveIteratorIterator
{
    function __construct($options = array())
    {
        // retrieve the left and right value of the $root node  
        //$root = UNL_Officefinder_Department::getByname('University of Nebraska - Lincoln');
        $root = UNL_Officefinder_Department::getByname('Office of University Communications');
        $iterator = new UNL_Officefinder_DepartmentList(array($root));

        parent::__construct($iterator, RecursiveIteratorIterator::SELF_FIRST);
    }
    
}