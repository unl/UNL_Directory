<?php
class UNL_Officefinder_DepartmentList_Filter_HasChildren extends FilterIterator
{
    function accept()
    {
        return $this->current()->hasChildren();
    }
}