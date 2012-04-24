<?php
class UNL_Officefinder_TreeView extends FilterIterator
{
    function __construct($options = array())
    {
        // retrieve the left and right value of the $root node  
        $root = UNL_Officefinder_Department::getByname('University of Nebraska-Lincoln');
        //$root = UNL_Officefinder_Department::getByname('Office of University Communications');
        $iterator = new UNL_Officefinder_DepartmentList(array($root));

        parent::__construct(new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST));
    }

    function accept()
    {
        if ($this->getInnerIterator()->current()->isOfficialDepartment()) {
            return true;
        }
        return false;
    }
    
}